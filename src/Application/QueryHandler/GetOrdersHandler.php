<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Query\GetOrders;
use App\Domain\Entity\Order;
use App\Domain\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetOrdersHandler
{
    public function __construct(
        private readonly OrderRepositoryInterface $orderRepository,
    ) {
    }

    /**
     * @return list<Order>
     */
    public function __invoke(GetOrders $query): array
    {
        return $this->orderRepository->findAllOrdersWithOrderItems();
    }
}
