<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    /**
     * @return list<Order>
     */
    public function findAllOrdersWithOrderItems(): array;
}
