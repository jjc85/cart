<?php

declare(strict_types=1);

namespace App\Application\QueryHandler;

use App\Application\Query\GetCart;
use App\Domain\Entity\Cart;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Serializer\SerializerInterface;

#[AsMessageHandler]
final class GetCartHandler
{
    private SessionInterface $session;
    private SerializerInterface $serializer;

    public function __construct(RequestStack $requestStack, SerializerInterface $serializer)
    {
        $this->session = $requestStack->getSession();
        $this->serializer = $serializer;
    }

    public function __invoke(GetCart $query): string
    {
        /* @var Cart $cart */
        $cart = $this->session->get('cart', new Cart());

        return $this->serializer->serialize($cart, 'json');
    }
}
