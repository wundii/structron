<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

final readonly class StructronCollectionDto
{
    /**
     * @param StructronRowDto[] $collection
     */
    public function __construct(
        private string $pathname,
        private string $classname,
        private array $collection,
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
}
