<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Order;
use App\Domain\Repository\OrderRepositoryInterface;
use App\Infrastructure\Persistence\Entity\Order as OrderEntity;
use App\Infrastructure\Persistence\Mapper\OrderMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository implements OrderRepositoryInterface
{
    private OrderMapper $orderMapper;

    public function __construct(ManagerRegistry $registry, OrderMapper $orderMapper)
    {
        parent::__construct($registry, OrderEntity::class);
        $this->orderMapper = $orderMapper;
    }

    /**
     * @return list<Order>
     */
    public function findAllOrdersWithOrderItems(): array
    {
        $orderEntities = $this->createQueryBuilder('o')
        ->leftJoin('o.orderItems', 'oi')
        ->addSelect('oi')
        ->orderBy('o.createdAt', 'DESC')
        ->getQuery()->getResult();

        return array_map(function (OrderEntity $orderEntity) {
            return $this->orderMapper->toDomain($orderEntity);
        }, $orderEntities);
    }
}
