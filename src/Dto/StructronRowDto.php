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
        private null|bool|int|float|string $default = null,
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

    public function getType(): string
    {
        return (string) $this->type;
    }

    public function getDefault(): int|float|string
    {
        if ($this->default === null) {
            return 'null';
        }

        if (is_bool($this->default)) {
            return $this->default ? 'true' : 'false';
        }

        return $this->default;
    }

    public function getDescription(): string
    {
        return (string) $this->description;
    }
}
