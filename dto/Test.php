<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;

#[Structron('A product option DTO')]
#[Structron('This DTO represents a product option with an identifier and a name.')]
#[Structron('It is used to define various options that can be associated with a product.')]
#[Approach(ApproachEnum::CONSTRUCTOR)]
final class Test
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
