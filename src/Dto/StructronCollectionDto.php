<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

final readonly class StructronCollectionDto
{
    /**
     * @param StructronFileDto[] $structronFileDtos
     */
    public function __construct(
        private array $structronFileDtos,
    ) {
    }

    /**
     * @return StructronFileDto[]
     */
    public function getStructronFileDtos(): array
    {
        return $this->structronFileDtos;
    }
}
