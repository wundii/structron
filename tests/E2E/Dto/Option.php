<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\E2E\Dto;

use Wundii\Structron\Attribute\Description;

final class Option
{
    public function __construct(
        #[Description('The unique identifier for the option')]
        private int $optionId,
        #[Description('The name of the option')]
        private string $optionName,
    ) {
    }

    public function getOptionId(): int
    {
        return $this->optionId;
    }

    public function getOptionName(): string
    {
        return $this->optionName;
    }
}
