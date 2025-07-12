<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller;

use App\Application\Command\CartAddProductSessionCommand;
use App\Application\Command\CartRemoveProductSessionCommand;
use App\Application\Command\CartUpdateProductSessionCommand;
use App\Application\Command\CommandInterface;
use App\Application\DTO\CartAddProductPayload;
use App\Application\DTO\CartRemoveProductPayload;
use App\Application\DTO\CartUpdateProductPayload;
use App\Application\Query\GetCart;
use App\Application\Query\QueryInterface;
use App\Domain\ValueObject\Quantity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cart')]
class CartController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    #[Route('/add', name: 'cart_add', methods: ['POST'])]
    public function add(#[MapRequestPayload] CartAddProductPayload $carAddProductPayload): JsonResponse
    {
        $command = new CartAddProductSessionCommand($carAddProductPayload->productId);

        return $this->dispatchCommand($command, 'Product added to cart');
    }

    #[Route('/remove', name: 'cart_remove', methods: ['POST'])]
    public function remove(#[MapRequestPayload] CartRemoveProductPayload $cartRemoveProductPayload): JsonResponse
    {
        $command = new CartRemoveProductSessionCommand($cartRemoveProductPayload->productId);

        return $this->dispatchCommand($command, 'Product removed from cart');
    }

    #[Route('/update', name: 'cart_update', methods: ['POST'])]
    public function update(#[MapRequestPayload] CartUpdateProductPayload $cartUpdateProductPayload): JsonResponse
    {
        $command = new CartUpdateProductSessionCommand(
            $cartUpdateProductPayload->productId,
            new Quantity($cartUpdateProductPayload->quantity)
        );

        return $this->dispatchCommand($command, 'Cart updated');
    }

    #[Route('/view', name: 'cart_view', methods: ['GET'])]
    public function view(): JsonResponse
    {
        $query = new GetCart();

        return $this->dispatchQuery($query);
    }

    private function dispatchCommand(CommandInterface $command, string $successMessage): JsonResponse
    {
        try {
            $this->messageBus->dispatch($command);

            return new JsonResponse(
                ['message' => $successMessage],
                Response::HTTP_OK
            );
        } catch (HandlerFailedException $exception) {
            $innerException = $exception->getPrevious();
            $errorMessage = $innerException ? $innerException->getMessage() : $exception->getMessage();

            return new JsonResponse(
                ['message' => $errorMessage],
                Response::HTTP_BAD_REQUEST
            );
        }
    }

    private function dispatchQuery(QueryInterface $query): JsonResponse
    {
        try {
            $envelope = $this->messageBus->dispatch($query);
        } catch (HandlerFailedException $exception) {
            $innerException = $exception->getPrevious();
            $errorMessage = $innerException ? $innerException->getMessage() : $exception->getMessage();

            return new JsonResponse(
                ['message' => $errorMessage],
                Response::HTTP_BAD_REQUEST
            );
        }

        $serializedObject = $envelope->last(HandledStamp::class)?->getResult() ?? json_encode([]);

        return new JsonResponse($serializedObject, Response::HTTP_OK, [], true);
    }
}
