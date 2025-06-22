<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::TARGET_METHOD)]
class Description implements AttributeInterface
{
    public function __construct(
        private string $description,
    ) {
    }

    public function getName(): ?string
    {
        return null;
    }

    public function getValue(): string
    {
        return $this->description;
    }
}
