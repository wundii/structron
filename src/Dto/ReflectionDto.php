<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

final readonly class ReflectionDto
{
    public function __construct(
        private string $pathname,
        private string $className,
        private string $classShortName,
    ) {
    }

    public function getPathname(): string
    {
        return $this->pathname;
    }

    public function getClassName(): string
    {
        return $this->className;
    }

    public function getClassShortName(): string
    {
        return $this->classShortName;
    }
}
