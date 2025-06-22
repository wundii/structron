<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final readonly class Structron implements AttributeInterface
{
    public function __construct(
        private string $description,
    ) {
    }

    public function getName(): string
    {
        return 'structron.structron';
    }

    public function getValue(): ?string
    {
        return $this->description;
    }
}
