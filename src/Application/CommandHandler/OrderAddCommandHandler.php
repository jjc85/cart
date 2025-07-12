<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\OrderAddCommand;
use App\Domain\Entity\Cart;
use App\Domain\Entity\Order;
use App\Domain\Entity\OrderItem;
use App\Domain\Event\OrderCreatedEvent;
use App\Infrastructure\Persistence\Mapper\OrderMapper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class OrderAddCommandHandler
{
    private SessionInterface $session;
    private EntityManagerInterface $entityManager;
    private OrderMapper $orderMapper;
    private MessageBusInterface $messageBus;

    public function __construct(
        RequestStack $requestStack,
        EntityManagerInterface $entityManager,
        OrderMapper $orderMapper,
        MessageBusInterface $messageBus,
    ) {
        $this->session = $requestStack->getSession();
        $this->entityManager = $entityManager;
        $this->orderMapper = $orderMapper;
        $this->messageBus = $messageBus;
    }

    public function __invoke(OrderAddCommand $command): void
    {
        /** @var Cart $cart */
        $cart = $this->session->get('cart', new Cart());

        if ($cart->isEmpty()) {
            throw new \RuntimeException('Cart is empty, cannot create order.');
        }

        $order = $this->cartToOrder($cart);

        $orderEntity = $this->orderMapper->toEntity($order);
        $orderEntity->setTotalAmount($cart->getTotalPrice());
        $this->entityManager->persist($orderEntity);
        $this->entityManager->flush();

        $this->messageBus->dispatch(new OrderCreatedEvent($orderEntity->getid()));

        $this->session->set('cart', new Cart());
    }

    public function cartToOrder(Cart $cart): Order
    {
        $orderItems = [];

        foreach ($cart->getCartItems() as $cartItem) {
            $orderItems[] = new OrderItem(
                null,
                $cartItem->getProductName(),
                $cartItem->getQuantity(),
                $cartItem->getPrice()
            );
        }

        return new Order(null, $orderItems);
    }
}
