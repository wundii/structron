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
        $implodeType = explode('\\', (string) $this->type);

        return array_pop($implodeType);
    }

    public function getTypeRaw(): string
    {
        return str_replace('[]', '', $this->getType());
    }

    public function getTypeRawIfClass(): ?string
    {
        if ($this->type === null) {
            return null;
        }

        $implodeType = explode('\\', $this->type);

        if (count($implodeType) === 1) {
            return null;
        }

        return str_replace('[]', '', $this->type);
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
        return $this->cleanUp((string) $this->description);
    }

    public function cleanUp(string $string): string
    {
        $string = (string) preg_replace("/\r\n|\r|\n/", '', $string);
        return (string) preg_replace('/\s+/', ' ', $string);
    }
}
