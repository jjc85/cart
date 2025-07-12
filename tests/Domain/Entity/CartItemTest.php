<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\CartItem;
use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CartItem::class)]
final class CartItemTest extends TestCase
{
    public function testOnCreateCartItemThenReturnQuantityOne(): void
    {
        $product = new Product(1, 'Test Product', new Money(100));
        $cartItem = new CartItem($product);

        $this->assertSame(1, $cartItem->getQuantity()->getValue());
    }

    public function testOnSetQuantityCartItemThenUpdateQuantity(): void
    {
        $product = new Product(1, 'Test Product', new Money(100));
        $cartItem = new CartItem($product);

        $cartItem->setQuantity(new Quantity(2));

        $this->assertSame(2, $cartItem->getQuantity()->getValue());
    }
}
