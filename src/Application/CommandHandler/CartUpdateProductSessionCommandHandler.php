<?php

declare(strict_types=1);

namespace App\Application\CommandHandler;

use App\Application\Command\CartUpdateProductSessionCommand;
use App\Domain\Entity\Cart;
use App\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CartUpdateProductSessionCommandHandler
{
    private SessionInterface $session;
    private ProductRepositoryInterface $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepositoryInterface $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
    }

    public function __invoke(CartUpdateProductSessionCommand $command): void
    {
        /** @var Cart $cart */
        $cart = $this->session->get('cart', new Cart());

        $productId = $command->getProductId();
        $quantity = $command->getQuantity();
        $product = $this->productRepository->findOne($productId);

        $cart->updateProduct($product, $quantity);
        $this->session->set('cart', $cart);
    }
}
