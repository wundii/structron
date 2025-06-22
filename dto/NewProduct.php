<?php

declare(strict_types=1);

namespace Wundii\Structron\Dto;

use Wundii\Structron\Attribute\Description;
use Wundii\Structron\Attribute\Structron;

#[Structron('A new product DTO')]
#[Structron('This DTO represents a new product with various attributes such as product ID, name, number, EAN, and tags.')]
final class NewProduct
{
    /**
     * @param string[] $tags
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
}
