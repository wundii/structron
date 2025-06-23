<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver;

use Exception;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronFileDto;
use Wundii\Structron\Dto\StructronRowDto;
use Wundii\Structron\Enum\StructronRowTypEnum;

final class StructronFileResolver
{
    private ReflectionObjectResolver $reflectionObjectResolver;

    /**
     * @var array<string, ObjectPropertyDto>
     */
    private array $objectPropertyDtos = [];

    public function __construct()
    {
        $this->reflectionObjectResolver = new ReflectionObjectResolver();
    }

    /**
     * @return iterable<StructronRowDto>
     * @throws DataMapperException|ReflectionException
     *      */
    public function objectDto(
        DataConfigInterface $dataConfig,
        ObjectPropertyDto $objectPropertyDto,
        null|string $object,
        null|string $prefix = null,
        int $prefixLevel = 0,
    ): iterable {
        if ($prefix) {
            ++ $prefixLevel;
        }

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        $targetObjectDto = $this->getObjectPropertyDto($object ?: '');

        foreach ($objectPropertyDto->getConstructor() as $propertyDto) {
            $propertyDto = $targetObjectDto->findPropertyDto($dataConfig->getApproach(), $propertyDto->getName());
            if (! $propertyDto instanceof PropertyDto) {
                continue;
            }

            $name = $propertyDto->getName();
            $dataType = $propertyDto->getDataType();
            $targetType = $propertyDto->getTargetType();
            $description = $this->findOneAttribute(
                $objectPropertyDto,
                Description::class,
                $name,
            );

            if ($description instanceof PropertyDto) {
                if (! is_string($description->getValue()) && $description->getValue() !== null) {
                    throw new RuntimeException('The description attribute must be a string, ' . gettype($description) . ' given');
                }

                $description = $description->getValue();
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

            if ($dataType === DataTypeEnum::ARRAY || $dataType === DataTypeEnum::OBJECT) {
                if (is_object($defaultValue)) {
                    $reflection = new ReflectionClass($defaultValue);
                    if ($reflection->isEnum()) {
                        $defaultValue = $reflection->getShortName() . '::' . $defaultValue->name;
                    }
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
                $outputName = str_repeat('&nbsp; ', $prefixLevel) . $prefix . '.' . $name;
            }

            if ($dataType === DataTypeEnum::ARRAY || $dataType === DataTypeEnum::OBJECT) {
                /** @var class-string $targetType */
                $reflection = new ReflectionClass($targetType);
                if (! $reflection->isInternal() && !$reflection->isEnum()) {
                    yield new StructronRowDto(
                        StructronRowTypEnum::SUBHEADER,
                        $outputName,
                        $dataType === DataTypeEnum::ARRAY ? $targetType . '[]' : $targetType,
                        $propertyDto->isDefaultValueAvailable() ? $defaultValue : 'required',
                        $description,
                    );

                    $targetType = $dataConfig->mapClassName((string) $targetType);

                    foreach ($this->objectDto($dataConfig, $this->getObjectPropertyDto($targetType), $targetType, $name, $prefixLevel) as $row) {
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

    public function findOneAttribute(
        ObjectPropertyDto $objectPropertyDto,
        string $classStringName,
        string $name,
    ): ?PropertyDto {
        foreach ($objectPropertyDto->getAttributes() as $propertyDto) {
            if (
                $propertyDto->getAttributeClassString() === $classStringName
                && $name === $propertyDto->getName()
            ) {
                return $propertyDto;
            }
        }

        return null;
    }

    /**
     * @return PropertyDto[]
     */
    public function findAttribute(
        ObjectPropertyDto $objectPropertyDto,
        string $classStringName,
        string $name,
    ): array {
        return array_filter(
            $objectPropertyDto->getAttributes(),
            static fn (PropertyDto $propertyDto): bool => $propertyDto->getAttributeClassString() === $classStringName
                && $name === $propertyDto->getName()
        );
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function getObjectPropertyDto(string $className): ObjectPropertyDto
    {
        if (array_key_exists($className, $this->objectPropertyDtos)) {
            $objectPropertyDto = $this->objectPropertyDtos[$className];
        } else {
            $objectPropertyDto = $this->reflectionObjectResolver->resolve($className);
            $this->objectPropertyDtos[$className] = $objectPropertyDto;
        }

        return $objectPropertyDto;
    }

    /**
     * @throws ReflectionException
     * @throws DataMapperException
     */
    public function resolve(ReflectionDto $reflectionDto): ?StructronFileDto
    {
        $objectPropertyDto = $this->getObjectPropertyDto($reflectionDto->getClassName());

        if (! array_filter(
            $objectPropertyDto->getAttributes(),
            static fn (PropertyDto $propertyDto): bool => $propertyDto->getAttributeClassString() === Structron::class
        )) {
            return null;
        }

        $structronAttributes = $this->findAttribute(
            $objectPropertyDto,
            Structron::class,
            'structron.structron'
        );
        $approachAttribute = $this->findOneAttribute(
            $objectPropertyDto,
            Approach::class,
            'structron.approach'
        );

        $approachEnum = ApproachEnum::CONSTRUCTOR;
        if ($approachAttribute instanceof PropertyDto) {
            $approachEnum = $approachAttribute->getValue();
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
                null,
                null,
                null,
            ),
        ];

        foreach ($this->objectDto($dataConfig, $objectPropertyDto, $reflectionDto->getClassName()) as $row) {
            $structronRowDto[] = $row;
        }

        return new StructronFileDto(
            $approachEnum,
            $reflectionDto->getPathname(),
            $reflectionDto->getClassName(),
            $structronRowDto,
            array_map(
                static fn (PropertyDto $propertyDto): string => is_string($propertyDto->getValue()) ? $propertyDto->getValue() : '',
                $structronAttributes
            )
        );
    }
}
