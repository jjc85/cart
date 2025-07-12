<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Money
{
    private float $value;

    public function __construct(float $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('The quantity cannot be negative');
        }

        $this->value = $value;
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function equals(Money $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function add(Money $other): Money
    {
        return new self($this->value + $other->getValue());
    }

    public function subtract(Money $other): Money
    {
        $newValue = $this->value - $other->getValue();
        if ($newValue < 0) {
            throw new \InvalidArgumentException('Subtraction would result in a negative number');
        }

        return new self($newValue);
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
