<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Command\OrderAddCommand;
use App\Application\Query\GetOrders;
use App\Domain\Entity\Order;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    private MessageBusInterface $queryBus;

    public function __construct(MessageBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    #[Route('/list', name: 'order_list', methods: ['GET'])]
    public function list(): Response
    {
        $query = new GetOrders();
        $envelope = $this->queryBus->dispatch($query);

        /** @var list<Order> $orders */
        $orders = $envelope->last(HandledStamp::class)?->getResult() ?? [];

        return $this->render('order/list.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/add', name: 'order_add', methods: ['POST'])]
    public function add(): JsonResponse
    {
        $command = new OrderAddCommand();
        $this->queryBus->dispatch($command);

        return $this->json(
            [
                'message' => 'Order created successfully',
            ],
            Response::HTTP_CREATED
        );
    }
}
