<?php

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\ValueObject\Money;
use App\Infrastructure\Persistence\Entity\Order as OrderEntity;
use App\Infrastructure\Persistence\Entity\OrderItem as OrderItemEntity;
use Doctrine\Common\Collections\ArrayCollection;

class OrderMapper
{
    private OrderItemMapper $orderItemMapper;

    public function __construct(OrderItemMapper $orderItemMapper)
    {
        $this->orderItemMapper = $orderItemMapper;
    }

    public function toEntity(Order $order): OrderEntity
    {
        $orderEntity = new OrderEntity();

        $OrderItemEntities = array_map(function (OrderItem $orderItemEntity) use ($orderEntity) {
            $orderItemEntity = $this->orderItemMapper->toEntity($orderItemEntity);
            $orderItemEntity->setOrder($orderEntity);

            return $orderItemEntity;
        }, $order->getOrderItems());

        $orderItemEntityCollection = new ArrayCollection($OrderItemEntities);
        $orderEntity->setOrderItems($orderItemEntityCollection);

        return $orderEntity;
    }

    public function toDomain(OrderEntity $orderEntity): Order
    {
        $orderItems = $orderEntity->getOrderItems()->map(function (OrderItemEntity $orderItemEntity) {
            return $this->orderItemMapper->toDomain($orderItemEntity);
        })->toArray();

        $order = new Order($orderEntity->getId(), $orderItems);
        $order->setTotalAmount(new Money($orderEntity->getTotalAmount()));
        $order->setCreatedAt($orderEntity->getCreatedAt());

        $order->setOrderItems($orderItems);

        return $order;
    }
}
