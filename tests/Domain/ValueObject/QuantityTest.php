<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\Quantity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Quantity::class)]
final class QuantityTest extends TestCase
{
    public function testOnCreateMoneyWithPositiveValueThenReturnsValue(): void
    {
        $quantity = new Quantity(10);
        $this->assertSame(10, $quantity->getValue());
    }

    public function testOnCreateMoneyWithNegativeValueThenThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The quantity cannot be negative');
        new Quantity(-5);
    }

    public function testOnEqualsQuantityWhenCompareTwwQuantityWithSameValueThenReturnsTrue(): void
    {
        $quantityA = new Quantity(15);
        $quantityB = new Quantity(15);
        $this->assertTrue($quantityA->equals($quantityB));
    }

    public function testOnEqualsQuantityWhenCompareTwoQuantityWithDistinctValueThenReturnsFalse(): void
    {
        $quantityA = new Quantity(15);
        $quantityB = new Quantity(25);
        $this->assertFalse($quantityA->equals($quantityB));
    }

    public function testOnAddThenReturnsTheValueSum(): void
    {
        $quantityA = new Quantity(10);
        $quantityB = new Quantity(5);
        $QuantityResult = $quantityA->add($quantityB);

        $this->assertSame(15, $QuantityResult->getValue());
    }

    public function testOnSubtractThenReturnsValueSubtract(): void
    {
        $quantityA = new Quantity(20);
        $quantityB = new Quantity(5);
        $quantityResult = $quantityA->subtract($quantityB);

        $this->assertSame(15, $quantityResult->getValue());
    }

    public function testOnSubtractWhenSubtractionResultsIsNegativeNumberThenThrowsInvalidArgumentException(): void
    {
        $quantityA = new Quantity(10);
        $quantityB = new Quantity(20);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Subtraction would result in a negative number');
        $quantityA->subtract($quantityB);
    }
}
