<?php

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\OrderItem;
use App\Domain\ValueObject\Money;
use App\Domain\ValueObject\Quantity;
use App\Infrastructure\Persistence\Entity\OrderItem as OrderItemEntity;

class OrderItemMapper
{
    public function toEntity(OrderItem $orderItem): OrderItemEntity
    {
        $orderItemEntity = new OrderItemEntity();
        $orderItemEntity->setProductName($orderItem->getProductName());
        $orderItemEntity->setQuantity($orderItem->getQuantity()->getValue());
        $orderItemEntity->setUnitPrice($orderItem->getPrice()->getValue());
        $orderItemEntity->setOrder(null);

        return $orderItemEntity;
    }

    public function toDomain(OrderItemEntity $orderItemEntity): OrderItem
    {
        return new OrderItem(
            $orderItemEntity->getId(),
            $orderItemEntity->getProductName(),
            new Quantity($orderItemEntity->getQuantity()),
            new Money($orderItemEntity->getUnitPrice())
        );
    }
}
