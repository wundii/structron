<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

final readonly class StructronFileDto
{
    /**
     * @param StructronRowDto[] $collection
     * @param string[] $descriptions
     */
    public function __construct(
        private string $pathname,
        private string $classname,
        private array $collection,
        private array $descriptions = [],
    ) {
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
