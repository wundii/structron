<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Structron
{
    public function __construct(
        private string $description,
    ) {
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}
