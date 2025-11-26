<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver;

use Exception;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Dto\AttributeDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\ReflectionObjectDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\AttributeOriginEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Parser\ReflectionClassParser;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;
use Wundii\Structron\Config\OptionEnum;
use Wundii\Structron\Config\StructronConfig;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronFileDto;
use Wundii\Structron\Dto\StructronRowDto;
use Wundii\Structron\Enum\StructronRowTypEnum;

final class StructronFileResolver
{
    /**
     * â@phpstan-ignore-next-line
     */
    private ReflectionClassParser $reflectionClassParser;

    /**
     * @var array<string, ReflectionObjectDto>
     */
    private static array $reflectionObjectDtos = [];

    public function __construct()
    {
        $this->reflectionClassParser = new ReflectionClassParser();
    }

    /**
     * @param string[] $processedClassNames
     * @return iterable<StructronRowDto>
     * @throws DataMapperException|ReflectionException
     * @throws Exception
     *      */
    public function objectDto(
        StructronConfig $structronConfig,
        DataConfigInterface $dataConfig,
        ReflectionObjectDto $reflectionObjectDto,
        null|string $object,
        array $processedClassNames = [],
        null|string $prefix = null,
        int $prefixLevel = 0,
    ): iterable {
        if ($prefix) {
            ++$prefixLevel;
        }

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);

            if (in_array($object, $processedClassNames, true)) {
                return;
            }

            $processedClassNames[] = $object;
        }

        $targetObjectDto = $this->getReflectionObjectDto($object ?: '');

        foreach ($reflectionObjectDto->getProperties() as $propertyDto) {
            $propertyDto = $targetObjectDto->findElementDto($dataConfig->getApproach(), $propertyDto->getName());
            if (! $propertyDto instanceof PropertyDto) {
                continue;
            }

            $name = $propertyDto->getName();
            $dataType = $propertyDto->getDataType();
            $targetType = $propertyDto->getTargetType();
            $description = $this->findOneAttribute(
                $reflectionObjectDto,
                [AttributeOriginEnum::TARGET_PROPERTY, AttributeOriginEnum::TARGET_METHOD],
                Description::class,
                $name,
            );

            if ($description instanceof AttributeDto) {
                if (! is_string($description->getArguments()['description'] ?? null) && $description->getArguments()['description'] !== null) {
                    throw new RuntimeException('The description attribute must be a string, ' . gettype($description) . ' given');
                }

                $description = $description->getArguments()['description'];
            }

            $defaultValue = $propertyDto->getDefaultValue();
            if ($dataType === DataTypeEnum::ARRAY) {
                $checkDataType = DataTypeEnum::fromString($targetType);
                if ($checkDataType instanceof DataTypeEnum) {
                    $dataType = $checkDataType->value . '[]';
                }

                if (is_array($defaultValue)) {
                    $defaultValue = $defaultValue !== [] ? '[...]' : '[]';
                }
            }

            if (($dataType === DataTypeEnum::ARRAY || $dataType === DataTypeEnum::OBJECT) && is_object($defaultValue)) {
                $reflection = new ReflectionClass($defaultValue);
                if ($reflection->isEnum()) {
                    /** @phpstan-ignore-next-line */
                    $defaultValue = $reflection->getShortName() . '::' . $defaultValue->name;
                }
            }

            if (
                ! is_bool($defaultValue)
                && ! is_int($defaultValue)
                && ! is_float($defaultValue)
                && ! is_string($defaultValue)
                && $defaultValue !== null
            ) {
                throw new Exception(sprintf('Property %s has an invalid default value type %s, required is null/bool/int/float/string', $name, gettype($defaultValue)));
            }

            $defaultValue = is_bool($defaultValue) ? ($defaultValue ? 'true' : 'false') : $defaultValue;

            $outputName = $name;
            if ($prefix) {
                $outputName = $prefix . '.' . $name;

                if ($structronConfig->getBoolean(OptionEnum::INDENT_FILE_ITERATION)) {
                    $outputName = str_repeat('&nbsp; ', $prefixLevel) . $outputName;
                }
            }

            if ($dataType === DataTypeEnum::ARRAY || $dataType === DataTypeEnum::OBJECT) {
                /** @var class-string $targetType */
                $reflection = new ReflectionClass($targetType);
                if (! $reflection->isInternal() && ! $reflection->isEnum()) {
                    yield new StructronRowDto(
                        StructronRowTypEnum::SUBHEADER,
                        $outputName,
                        $dataType === DataTypeEnum::ARRAY ? $targetType . '[]' : $targetType,
                        $propertyDto->isDefaultValueAvailable() ? $defaultValue : 'required',
                        $description,
                    );

                    $targetType = $dataConfig->mapClassName((string) $targetType);

                    foreach ($this->objectDto(
                        $structronConfig,
                        $dataConfig,
                        $this->getReflectionObjectDto($targetType),
                        $targetType,
                        $processedClassNames,
                        $name,
                        $prefixLevel,
                    ) as $row) {
                        yield $row;
                    }

                    continue;
                }

                $dataType = $targetType;
            }

            yield new StructronRowDto(
                StructronRowTypEnum::ROW,
                $outputName,
                $dataType instanceof DataTypeEnum ? $dataType->value : $dataType,
                $propertyDto->isDefaultValueAvailable() ? $defaultValue : 'required',
                $description,
            );
        }
    }

    /**
     * @param AttributeOriginEnum[] $attributeOriginEnums
     */
    public function findOneAttribute(
        ReflectionObjectDto $reflectionObjectDto,
        array $attributeOriginEnums,
        string $classStringName,
        ?string $name = null,
    ): ?AttributeDto {
        foreach ($reflectionObjectDto->getAttributes() as $attributeDto) {
            if (
                $name
                && $attributeDto->getClassString() === $classStringName
                && in_array($attributeDto->getAttributeOriginEnum(), $attributeOriginEnums, true)
                && $name === $attributeDto->getOriginName()
            ) {
                return $attributeDto;
            }

            if (
                $name === null
                && $attributeDto->getClassString() === $classStringName
                && in_array($attributeDto->getAttributeOriginEnum(), $attributeOriginEnums, true)
            ) {
                return $attributeDto;
            }
        }

        return null;
    }

    /**
     * @param AttributeOriginEnum[] $attributeOriginEnums
     * @return AttributeDto[]
     */
    public function findAttribute(
        ReflectionObjectDto $reflectionObjectDto,
        array $attributeOriginEnums,
        string $classStringName,
    ): array {
        return array_filter(
            $reflectionObjectDto->getAttributes(),
            static fn (AttributeDto $attributeDto): bool => $attributeDto->getClassString() === $classStringName
                && in_array($attributeDto->getAttributeOriginEnum(), $attributeOriginEnums, true)
        );
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function getReflectionObjectDto(string $className): ReflectionObjectDto
    {
        if (array_key_exists($className, self::$reflectionObjectDtos)) {
            $reflectionObjectDto = self::$reflectionObjectDtos[$className];
        } else {
            /** @var class-string $className */
            $reflectionObjectDto = $this->reflectionClassParser->parse($className);
            self::$reflectionObjectDtos[$className] = $reflectionObjectDto;
        }

        return $reflectionObjectDto;
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function resolve(
        StructronConfig $structronConfig,
        ReflectionDto $reflectionDto,
    ): ?StructronFileDto {
        $reflectionObjectDto = $this->getReflectionObjectDto($reflectionDto->getClassName());

        if (! array_filter(
            $reflectionObjectDto->getAttributes(),
            static fn (AttributeDto $attributeDto): bool => $attributeDto->getClassString() === Structron::class
        )) {
            return null;
        }

        $structronAttributes = $this->findAttribute(
            $reflectionObjectDto,
            [AttributeOriginEnum::TARGET_CLASS],
            Structron::class,
        );

        $approachAttribute = $this->findOneAttribute(
            $reflectionObjectDto,
            [AttributeOriginEnum::TARGET_CLASS],
            Approach::class,
        );

        $approachEnum = ApproachEnum::CONSTRUCTOR;
        if ($approachAttribute instanceof AttributeDto) {
            $approachEnum = $approachAttribute->getArguments()['approachEnum'] ?? null;
            if (! $approachEnum instanceof ApproachEnum) {
                throw new RuntimeException(
                    'The approach attribute must be an instance of ' . ApproachEnum::class
                );
            }
        }

        $dataConfig = new DataConfig(
            $approachEnum,
            AccessibleEnum::PUBLIC,
            [
                \DateTimeInterface::class => \DateTime::class,
            ]
        );

        $structronRowDto = [
            new StructronRowDto(
                StructronRowTypEnum::HEADER,
                $reflectionDto->getClassShortName(),
            ),
        ];

        foreach ($this->objectDto($structronConfig, $dataConfig, $reflectionObjectDto, $reflectionDto->getClassName()) as $row) {
            $structronRowDto[] = $row;
        }

        return new StructronFileDto(
            $reflectionDto->getPathname(),
            $reflectionDto->getClassName(),
            $structronRowDto,
            array_map(
                static fn (AttributeDto $attributeDto): string
                    => is_string($attributeDto->getArguments()['description'] ?? null)
                    ? $attributeDto->getArguments()['description']
                    : '',
                $structronAttributes
            )
        );
    }
}
