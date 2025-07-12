<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;

/**
 * @codeCoverageIgnore
 */
final class OrderItem
{
    private ?int $id;
    private string $productName;
    private Quantity $quantity;
    private Money $price;

    public function __construct(?int $id, string $productName, Quantity $quantity, Money $price)
    {
        $this->id = $id;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): Quantity
    {
        return $this->quantity;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }
}
