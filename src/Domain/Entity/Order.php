<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\Money;

/**
 * @codeCoverageIgnore
 */
final class Order
{
    private ?int $id;
    /**
     * @var list<OrderItem>
     */
    private array $orderItems;

    private Money $totalAmount;

    private \DateTimeImmutable $createdAt;

    /**
     * @param list<OrderItem> $orderItems
     */
    public function __construct(?int $id, array $orderItems)
    {
        $this->id = $id;
        $this->orderItems = $orderItems;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return list<OrderItem>
     */
    public function getOrderItems(): array
    {
        return $this->orderItems;
    }

    /**
     * @param list<OrderItem> $orderItems
     */
    public function setOrderItems(array $orderItems): void
    {
        $this->orderItems = $orderItems;
    }

    public function getTotalAmount(): Money
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(Money $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
