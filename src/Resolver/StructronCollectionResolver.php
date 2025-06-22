<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver;

use ReflectionException;
use RuntimeException;
use Wundii\DataMapper\DataConfig;
use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Dto\Type\ArrayDto;
use Wundii\DataMapper\Dto\Type\BoolDto;
use Wundii\DataMapper\Dto\Type\FloatDto;
use Wundii\DataMapper\Dto\Type\IntDto;
use Wundii\DataMapper\Dto\Type\NullDto;
use Wundii\DataMapper\Dto\Type\ObjectDto;
use Wundii\DataMapper\Dto\Type\StringDto;
use Wundii\DataMapper\Enum\AccessibleEnum;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Enum\DataTypeEnum;
use Wundii\DataMapper\Exception\DataMapperException;
use Wundii\DataMapper\Interface\ArrayDtoInterface;
use Wundii\DataMapper\Interface\DataConfigInterface;
use Wundii\DataMapper\Interface\ObjectDtoInterface;
use Wundii\DataMapper\Resolver\ObjectDtoResolver;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Structron;
use Wundii\Structron\Dto\ReflectionDto;
use Wundii\Structron\Dto\StructronCollectionDto;
use Wundii\Structron\Dto\StructronRowDto;
use Wundii\Structron\Enum\StructronRowTypEnum;

class StructronCollectionResolver
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
     * @return PropertyDto[]
     */
    private function arrayToPropertyDtos(PropertyDto $propertyDto): array
    {
        $propertyDtos = [];

        $array = $propertyDto->getValue();
        if (is_iterable($array)) {
            foreach ($array as $key => $value) {
                $propertyDtos[$key] = new PropertyDto(
                    $propertyDto->getName(),
                    $propertyDto->getDataType(),
                    $propertyDto->getTargetType(),
                    $propertyDto->isOneType(),
                    $propertyDto->isNullable(),
                    $propertyDto->getAccessibleEnum(),
                    $value,
                    $propertyDto->getAttributeClassString(),
                );
            }
        }

        return $propertyDtos;
    }

    /**
     * @param PropertyDto[] $availableDataList
     * @throws DataMapperException|ReflectionException
     */
    public function elementArray(
        DataConfigInterface $dataConfig,
        array $availableDataList,
        null|string $type,
        null|string $destination = null,
    ): ArrayDtoInterface
    {
        $dataList = [];
        $dataType = DataTypeEnum::fromString($type);
        if (class_exists((string)$type)) {
            $dataType = DataTypeEnum::OBJECT;
        }

        if (!$dataType instanceof DataTypeEnum) {
            throw DataMapperException::Error(sprintf('Element array invalid element data type %s for the target %s', $type, $destination));
        }

        foreach ($availableDataList as $availableData) {
            $name = $availableData->getName();
            $value = $availableData->getStringValue();
            $objectPropertyDto = null;

            if ($dataType === DataTypeEnum::OBJECT) {
                $object = $availableData->getValue();
                if (!is_object($object)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $objectPropertyDto = (new ReflectionObjectResolver())->resolve($object, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::OBJECT => $this->objectDto($dataConfig, $objectPropertyDto, $type, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
                default => throw DataMapperException::Error(sprintf('Element array invalid element data type %s for the target %s', $type, $name)),
            };

            /**
             * Skip objects with empty data in the array.
             */
            if ($dataType === DataTypeEnum::OBJECT && $data->getValue() === []) {
                continue;
            }

            $dataList[] = $data;
        }

        return new ArrayDto($dataList, $destination);
    }

    /**
     * @throws DataMapperException|ReflectionException
     */
    public function objectDto(
        DataConfigInterface $dataConfig,
        ObjectPropertyDto $objectPropertyDto,
        null|string $object,
        null|string $destination = null,
    ): ObjectDtoInterface
    {
        $dataList = [];

        if (is_string($object)) {
            $object = $dataConfig->mapClassName($object);
        }

        $targetObjectDto = $this->getObjectPropertyDto($object ?: '');

        foreach ($objectPropertyDto->availableData() as $availableData) {
            $propertyDto = $targetObjectDto->findPropertyDto($dataConfig->getApproach(), $availableData->getName());
            if (!$propertyDto instanceof PropertyDto) {
                continue;
            }

            $value = $availableData->getStringValue();
            $name = $propertyDto->getName();
            $dataType = $propertyDto->getDataType();
            $targetType = $propertyDto->getTargetType();
            $childPropertyDtos = [];
            $childObjectDto = null;

            if ($propertyDto->isNullable() && $value === '') {
                $dataType = DataTypeEnum::NULL;
            }

            if ($dataType === DataTypeEnum::ARRAY) {
                $childPropertyDtos = $this->arrayToPropertyDtos($availableData);
            }

            if ($dataType === DataTypeEnum::OBJECT) {
                $objectDtoValue = $availableData->getValue();
                if (!is_object($objectDtoValue)) {
                    throw DataMapperException::Error(sprintf('Element array invalid object type for %s', $name));
                }

                $childObjectDto = (new ReflectionObjectResolver())->resolve($objectDtoValue, true);
            }

            $data = match ($dataType) {
                DataTypeEnum::NULL => new NullDto($name),
                DataTypeEnum::INTEGER => new IntDto($value, $name),
                DataTypeEnum::FLOAT => new FloatDto($value, $name),
                DataTypeEnum::BOOLEAN => new BoolDto($value, $name),
                DataTypeEnum::ARRAY => $this->elementArray($dataConfig, $childPropertyDtos, $targetType, $name),
                DataTypeEnum::OBJECT => $this->objectDto($dataConfig, $childObjectDto, $targetType, $name),
                DataTypeEnum::STRING => new StringDto($value, $name),
                default => throw DataMapperException::Error(sprintf('Element object invalid element data type for the target %s', $name)),
            };

            $dataList[] = $data;
        }

        return new ObjectDto($object ?: '', $dataList, $destination);
    }

    public function getObjectPropertyDto(object|string $className): ObjectPropertyDto
    {
        if (is_object($className)) {
            $className = get_class($className);
        }

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
    public function resolve(ReflectionDto $reflectionDto): ?StructronCollectionDto
    {
        $objectPropertyDto = $this->getObjectPropertyDto($reflectionDto->getClassName());

        if (! array_filter(
            $objectPropertyDto->getAttributes(),
            static fn (PropertyDto $propertyDto): bool => $propertyDto->getAttributeClassString() === Structron::class
        )) {
            return null;
        }

        $approachArray = array_filter(
            $objectPropertyDto->getAttributes(),
            static fn (PropertyDto $propertyDto): bool => $propertyDto->getAttributeClassString() === Approach::class
        );

        $approachEnum = ApproachEnum::CONSTRUCTOR;
        if ($approachArray !== []) {
            $propertyDto = array_pop($approachArray);
            $approachEnum = $propertyDto->getValue();
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

        $objectDto = $this->objectDto($dataConfig, $objectPropertyDto, $reflectionDto->getClassName());

        // dd($objectDto);


        return new StructronCollectionDto(
            $approachEnum,
            $reflectionDto->getPathname(),
            $reflectionDto->getClassName(),
            [
                new StructronRowDto(
                    StructronRowTypEnum::HEADER,
                    $reflectionDto->getClassShortName(),
                    null,
                    null,
                    null,
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'name',
                    'string',
                    null,
                    null,
                ),
                new StructronRowDto(
                    StructronRowTypEnum::ROW,
                    'id',
                    'int',
                    null,
                    null,
                ),
            ],
        );
    }
}
