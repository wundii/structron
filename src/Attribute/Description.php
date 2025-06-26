<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
final readonly class Description
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
