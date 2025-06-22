<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;
use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\DataMapper\Interface\AttributeInterface;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Approach implements AttributeInterface
{
    public function __construct(
        private ApproachEnum $approachEnum,
    ) {
    }

    public function getName(): string
    {
        return 'structron.approach';
    }

    public function getValue(): ApproachEnum
    {
        return $this->approachEnum;
    }
}
