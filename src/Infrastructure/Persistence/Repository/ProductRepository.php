<?php

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Entity\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use App\Infrastructure\Persistence\Entity\Product as ProductEntity;
use App\Infrastructure\Persistence\Mapper\ProductMapper;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductRepository extends ServiceEntityRepository implements ProductRepositoryInterface
{
    private ProductMapper $mapper;

    public function __construct(ManagerRegistry $registry, ProductMapper $mapper)
    {
        parent::__construct($registry, ProductEntity::class);
        $this->mapper = $mapper;
    }

    public function findAll(): array
    {
        /** @var ProductEntity[] $productEntities */
        $productEntities = parent::findAll();

        return array_map(fn (ProductEntity $entity) => $this->mapper->toDomain($entity), $productEntities);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function findOne(int $id): Product
    {
        $productEntity = parent::find($id);

        if (null === $productEntity) {
            throw new NotFoundHttpException(sprintf('Product with ID %d not found', $id));
        }

        return $this->mapper->toDomain($productEntity);
    }
}
