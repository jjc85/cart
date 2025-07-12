<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Query\GetProducts;
use App\Domain\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    #[Route('/products', name: 'product_list', methods: ['GET'])]
    public function list(): Response
    {
        $query = new GetProducts();
        $envelope = $this->queryBus->dispatch($query);

        /** @var list<Product> $products */
        $products = $envelope->last(HandledStamp::class)?->getResult() ?? [];

        return $this->render('products/list.html.twig', [
            'products' => $products,
        ]);
    }
}
