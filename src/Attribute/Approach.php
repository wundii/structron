<?php

declare(strict_types=1);

namespace Wundii\Structron\Attribute;

use Attribute;
use Wundii\DataMapper\Enum\ApproachEnum;

#[Attribute(Attribute::TARGET_CLASS)]
final readonly class Approach
{
    public function __construct(
        private ApproachEnum $approachEnum,
    ) {
    }

    public function getApproachEnum(): ApproachEnum
    {
        return $this->approachEnum;
    }
}
