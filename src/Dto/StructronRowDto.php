<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

use Wundii\Structron\Enum\StructronRowTypEnum;

final readonly class StructronRowDto
{
    public function __construct(
        private StructronRowTypEnum $structronRowTypEnum,
        private string $name,
        private ?string $type = null,
        private ?string $default = null,
        private ?string $description = null,
    ) {
    }

    public function getStructronRowTypEnum(): StructronRowTypEnum
    {
        return $this->structronRowTypEnum;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
