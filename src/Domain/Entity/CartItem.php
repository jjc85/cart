<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

final class CartItem
{
    private string $productName;
    private Quantity $quantity;
    private Money $price;

    public function __construct(Product $product)
    {
        $this->productName = $product->getName();
        $this->price = $product->getPrice();
        $this->quantity = new Quantity(1);
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function setQuantity(Quantity $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }
}
