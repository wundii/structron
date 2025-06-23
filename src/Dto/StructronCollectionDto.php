<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

use Wundii\DataMapper\Enum\ApproachEnum;

final readonly class StructronCollectionDto
{
    /**
     * @param StructronRowDto[] $collection
     * @param string[] $descriptions
     */
    public function __construct(
        private ApproachEnum $approachEnum,
        private string $pathname,
        private string $classname,
        private array $collection,
        private array $descriptions = [],
    ) {
    }

    public function getApproachEnum(): ApproachEnum
    {
        return $this->approachEnum;
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getClassname(): string
    {
        return $this->classname;
    }

    /**
     * @return StructronRowDto[]
     */
    public function getCollection(): array
    {
        return $this->collection;
    }

    /**
     * @return string[]
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }
}
