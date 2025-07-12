<?php

namespace App\Infrastructure\Persistence\Mapper;

use App\Domain\Entity\Product;
use App\Domain\ValueObject\Money;
use App\Infrastructure\Persistence\Entity\Product as ProductEntity;

class ProductMapper
{
    public function toEntity(Product $product): ProductEntity
    {
        $productEntity = new ProductEntity();
        $productEntity->setName($product->getName());
        $productEntity->setPrice($product->getPrice()->getValue());

        return $productEntity;
    }

    public function toDomain(ProductEntity $productEntity): Product
    {
        return new Product(
            $productEntity->getId(),
            $productEntity->getName(),
            new Money($productEntity->getPrice()),
        );
    }
}
