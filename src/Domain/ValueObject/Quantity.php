<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

final class Quantity
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('The quantity cannot be negative');
        }

        $this->value = $value;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(Quantity $other): bool
    {
        return $this->value === $other->getValue();
    }

    public function add(Quantity $other): Quantity
    {
        return new self($this->value + $other->getValue());
    }

    public function subtract(Quantity $other): Quantity
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
