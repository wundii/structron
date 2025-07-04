<?php

declare(strict_types=1);

namespace Wundii\Structron\Tests\E2E\Dto;

use Wundii\DataMapper\Enum\ApproachEnum;
use Wundii\Structron\Attribute\Approach;
use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;

#[Structron('A new product DTO')]
#[Structron('This DTO represents a new product with various attributes such as product ID, name, number, EAN, and tags.')]
#[Approach(ApproachEnum::CONSTRUCTOR)]
final class Product
{
    /**
     * @param string[] $tags
     * @param Option[] $options
     */
    public function __construct(
        #[Description('The unique identifier for the product')]
        private int $productId,
        #[Description('The name of the product')]
        private string $productName,
        #[Description('The product number, can be null')]
        private ?int $productNumber = null,
        #[Description('The EAN (European Article Number) of the product, can be null')]
        private ?string $ean = null,
        #[Description('An array of tags associated with the product')]
        private array $tags = [],
        #[Description('An optional option associated with the product, can be null')]
        private ?Option $option = null,
        #[Description('Additional options for the product, can be empty')]
        private array $options = [],
        #[Description('An enum representing a test value')]
        private TestEnum $testEnum = TestEnum::A,
    ) {
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    public function getProductNumber(): ?int
    {
        return $this->productNumber;
    }

    public function setProductNumber(?int $productNumber): void
    {
        $this->productNumber = $productNumber;
    }

    public function getEan(): ?string
    {
        return $this->ean;
    }

    public function setEan(?string $ean): void
    {
        $this->ean = $ean;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string[] $tags
     */
    public function setTags(array $tags): void
    {
        $this->tags = $tags;
    }

    public function getOption(): ?Option
    {
        return $this->option;
    }

    /**
     * @return Option[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getTestEnum(): TestEnum
    {
        return $this->testEnum;
    }

    public function setTestEnum(TestEnum $testEnum): void
    {
        $this->testEnum = $testEnum;
    }
}
