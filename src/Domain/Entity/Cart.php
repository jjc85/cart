<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Quantity;

final class Cart
{
    /**
     * @var list<CartItem>
     */
    private array $cartItems = [];

    public function addProduct(Product $product): void
    {
        $productId = $product->getId();

        if (isset($this->cartItems[$productId])) {
            $existingCartItem = $this->cartItems[$productId];
            $existingCartItemQuantity = $existingCartItem->getQuantity();
            $existingCartItem->setQuantity($existingCartItemQuantity->add(new Quantity(1)));

            return;
        }

        $this->cartItems[$productId] = new CartItem($product);
    }

    public function removeProduct(Product $product): void
    {
        $productId = $product->getId();

        if (isset($this->cartItems[$productId])) {
            unset($this->cartItems[$productId]);
        }
    }

    public function updateProduct(Product $product, Quantity $quantity): void
    {
        $productId = $product->getId();

        if (!isset($this->cartItems[$productId])) {
            return;
        }

        $cartItem = $this->cartItems[$productId];

        if ($quantity->getValue() <= 0) {
            $this->removeProduct($product);

            return;
        }

        $cartItem->setQuantity($quantity);
    }

    /**
     * @return list<CartItem>
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    public function getTotalPrice(): float
    {
        $totalPrice = 0.0;

        foreach ($this->cartItems as $cartItem) {
            $totalPrice += $cartItem->getPrice()->getValue() * $cartItem->getQuantity()->getValue();
        }

        return $totalPrice;
    }

    public function isEmpty(): bool
    {
        return [] === $this->cartItems;
    }

    public function clear(): void
    {
        $this->cartItems = [];
    }
}
