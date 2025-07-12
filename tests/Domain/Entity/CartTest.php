<?php

declare(strict_types=1);

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Cart;
use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Cart::class)]
final class CartTest extends TestCase
{
    public function testOnNewCartThenCartIsEmptyAndCartItemsCountIsZero(): void
    {
        $cart = new Cart();
        $this->assertTrue($cart->isEmpty());
        $this->assertCount(0, $cart->getCartItems());
    }

    public function testOnAddProductWhenProductNotExistInCartThenExistCartItemWithProductQuantityOneAndProductName()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(10));

        $cart->addProduct($product);

        $cartItem = $cart->getCartItems()[1];
        $this->assertSame(1, $cartItem->getQuantity()->getValue());
        $this->assertSame('Test Product', $cartItem->getProductName());
    }

    public function testOnAddProductWhenProductExistInCartThenExistCartItemWithProductQuantityPlusOneAndProductName()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(10));
        $cart->addProduct($product);

        $cart->addProduct($product);

        $cartItem = $cart->getCartItems()[1];
        $this->assertSame(2, $cartItem->getQuantity()->getValue());
        $this->assertSame('Test Product', $cartItem->getProductName());
    }

    public function testOnRemoveProductWhenProductExistInCartThenProductNotExist()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(10));
        $cart->addProduct($product);

        $cart->removeProduct($product);

        $this->assertTrue($cart->isEmpty());
    }

    public function testOnRemoveProductWhenProductNotExistInCartThenCartItemsNotChange()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(10));
        $cart->addProduct($product);
        $product2 = new Product(2, 'Test Product 2', new Money(10));

        $cart->removeProduct($product2);

        $cartItem = $cart->getCartItems()[1];
        $this->assertSame('Test Product', $cartItem->getProductName());
    }

    public function testOnUpdateProductWhenProductNotExistInCartThenCartItemsNotChange()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(10));

        $cart->updateProduct($product, new Quantity(4));

        $this->assertTrue($cart->isEmpty());
    }

    public function testOnUpdateProductWithPositiveQuantityWhenProductExistInCartThenCartItemQuantityChange()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(3));
        $cart->addProduct($product);

        $cart->updateProduct($product, new Quantity(4));

        $cartItem = $cart->getCartItems()[1];
        $this->assertSame(4, $cartItem->getQuantity()->getValue());
    }

    public function testOnUpdateProductWithZeroQuantityWhenProductExistInCartThenCartItemWithProductNotExist()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product', new Money(1));
        $cart->addProduct($product);

        $cart->updateProduct($product, new Quantity(0));

        $this->assertTrue($cart->isEmpty());
    }

    public function testOnGetTotalPriceWhenExistCartItemsThenReturnTotalPrice()
    {
        $cart = new Cart();
        $product1 = new Product(1, 'Test Product 1', new Money(1));
        $cart->addProduct($product1);

        $product2 = new Product(2, 'Test Product 2', new Money(2));
        $cart->addProduct($product2);

        $product3 = new Product(3, 'Test Product 3', new Money(3));
        $cart->addProduct($product3);

        $product4 = new Product(4, 'Test Product 4', new Money(4));
        $cart->addProduct($product4);

        $this->assertSame(10.0, $cart->getTotalPrice());
    }

    public function testOnClearThenCartItemIsEmpty()
    {
        $cart = new Cart();
        $product = new Product(1, 'Test Product 1', new Money(1));
        $cart->addProduct($product);

        $cart->clear();

        $this->assertEmpty($cart->getCartItems());
    }
}
