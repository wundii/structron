<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\E2E\Dto;

use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;

#[Structron('Loop Class')]
#[Approach(ApproachEnum::CONSTRUCTOR)]
final class Loop
{
    public function __construct(
        #[Description('Loop Property')]
        private Loop $loop,
    ) {
    }

    public function getLoop(): self
    {
        return $this->loop;
    }
}
