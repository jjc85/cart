<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Product;

interface ProductRepositoryInterface
{
    /**
     * @return list<Product>
     */
    public function findAll(): array;

    public function findOne(int $id): Product;
}
