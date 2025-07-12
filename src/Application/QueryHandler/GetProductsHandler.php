<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Query\GetProducts;
use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetProductsHandler
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    /**
     * @return list<Product>
     */
    public function __invoke(GetProducts $query): array
    {
        return $this->productRepository->findAll();
    }
}
