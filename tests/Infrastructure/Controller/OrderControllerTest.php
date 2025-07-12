<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use App\Domain\Entity\Order;
use App\Infrastructure\Controller\OrderController;
use App\Infrastructure\Persistence\Entity\Order as OrderEntity;
use App\Tests\Factory\ProductFactory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(OrderController::class)]
final class OrderControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    public const SERVER = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
    ];
    private const ADD_PRODUCT_CART_ENDPOINT = '/api/cart/add';
    private const ADD_ORDER_ENDPOINT = '/order/add';

    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function testOnAddOrderWhenExistCartInSessionThenSaveOrderInBD()
    {
        ProductFactory::createMany(5, [
            'price' => 1,
        ]);

        for ($i = 1; $i < 6; ++$i) {
            $payload['productId'] = $i;
            $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);
        }

        $this->getClientRequest([], self::ADD_ORDER_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Order created successfully', $responseDecoded['message']);

        $orderRepository = $this->entityManager->getRepository(OrderEntity::class);

        /** @var Order $order */
        $order = $orderRepository->find(1);

        self::assertSame(5.0, $order->getTotalAmount());
    }

    public function testOnAddOrderWhenCartNotExistInSessionThenReturnsErrorMessage()
    {
        $this->getClientRequest([], self::ADD_ORDER_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_INTERNAL_SERVER_ERROR);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertStringContainsString('Cart is empty, cannot create order', $responseDecoded['message']);
    }

    /**
     * @param array<string,string|int> $payload
     */
    private function getClientRequest(array $payload, string $endPoint, string $method = 'POST'): void
    {
        $this->client->request(
            $method,
            $endPoint,
            [],
            [],
            self::SERVER,
            json_encode($payload)
        );
    }
}
