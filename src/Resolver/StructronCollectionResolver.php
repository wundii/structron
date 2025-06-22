<?php

declare(strict_types=1);

namespace Wundii\Structron\Resolver;

use Wundii\DataMapper\Dto\ObjectPropertyDto;
use Wundii\DataMapper\Dto\PropertyDto;
use Wundii\DataMapper\Resolver\ReflectionObjectResolver;
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

    public function resolve(ReflectionDto $reflectionDto): ?StructronCollectionDto
    {
        if (array_key_exists($reflectionDto->getClassName(), $this->objectPropertyDtos)) {
            $objectPropertyDto = $this->objectPropertyDtos[$reflectionDto->getClassName()];
        } else {
            $objectPropertyDto = $this->reflectionObjectResolver->resolve($reflectionDto->getClassName());
            $this->objectPropertyDtos[$reflectionDto->getClassName()] = $objectPropertyDto;
        }

        if (! array_filter(
            $objectPropertyDto->getAttributes(),
            static fn (PropertyDto $propertyDto): bool => $propertyDto->getAttributeClassString() === Structron::class
        )) {
            return null;
        }

        return new StructronCollectionDto(
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
