<?php

declare(strict_types=1);

namespace App\Tests\Domain\ValueObject;

use App\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Money::class)]
final class MoneyTest extends TestCase
{
    public function testOnCreateMoneyWithPositiveValueThenReturnsValue(): void
    {
        $money = new Money(100.50);
        $this->assertSame(100.50, $money->getValue());
    }

    public function testOnCreateMoneyWithNegativeValueThenThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The quantity cannot be negative');
        new Money(-10.0);
    }

    public function testOnEqualsMoneyWhenCompareTwoMoneyWithSameValueThenReturnsTrue(): void
    {
        $moneyA = new Money(10.0);
        $moneyB = new Money(10.0);
        $this->assertTrue($moneyA->equals($moneyB));
    }

    public function testOnEqualsMoneyWhenCompareTwoMoneyWithDistinctValueThenReturnsFalse(): void
    {
        $moneyA = new Money(10.0);
        $moneyB = new Money(20.0);
        $this->assertFalse($moneyA->equals($moneyB));
    }

    public function testOnAddThenReturnsTheValueSum(): void
    {
        $moneyA = new Money(10.00);
        $moneyB = new Money(20.00);
        $moneyResult = $moneyA->add($moneyB);

        $this->assertSame(30.0, $moneyResult->getValue());
    }

    public function testOnSubtractThenReturnsValueSubtract(): void
    {
        $moneyA = new Money(20.0);
        $moneyB = new Money(5.0);
        $moneyResult = $moneyA->subtract($moneyB);

        $this->assertSame(15.0, $moneyResult->getValue());
    }

    public function testOnSubtractWhenSubtractionResultsIsNegativeNumberThenThrowsInvalidArgumentException(): void
    {
        $moneyA = new Money(10.0);
        $moneyB = new Money(20.0);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Subtraction would result in a negative number');
        $moneyA->subtract($moneyB);
    }
}
