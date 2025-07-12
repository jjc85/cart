<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface OrderRepositoryInterface
{
    /**
     * @return list<Product>
     */
    public function findAllOrdersWithOrderItems(): array;
}
