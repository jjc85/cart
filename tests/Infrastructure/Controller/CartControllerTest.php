<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Controller;

use App\Infrastructure\Controller\CartController;
use App\Tests\Factory\ProductFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

#[CoversClass(CartController::class)]
final class CartControllerTest extends WebTestCase
{
    use ResetDatabase;
    use Factories;

    /**
     * @var array<string, string>
     */
    public const array SERVER = [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
    ];

    private const string ADD_PRODUCT_CART_ENDPOINT = '/api/cart/add';
    private const string REMOVE_PRODUCT_CART_ENDPOINT = '/api/cart/remove';
    private const string UPDATE_PRODUCT_CART_ENDPOINT = '/api/cart/update';
    private const string VIEW_CART_ENDPOINT = '/api/cart/view';

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testOnAddProductCartWithValidRequestPayloadWhenProductExistInBDThenReturns200HttpCodeResponse(): void
    {
        ProductFactory::new()->create(
            ['name' => 'Test Product', 'price' => 1000]
        );

        $payload = [
            'productId' => 1,
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Product added to cart', $responseDecoded['message']);
    }

    public function testOnAddProductCartWithValidRequestPayloadWhenProductNotExistInBDThenReturns400HttpCodeResponse(): void
    {
        $payload = [
            'productId' => 1,
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Product with ID 1 not found', $responseDecoded['message']);
    }

    public function testOnAddProductCartWithStringInValidRequestPayloadThenReturns422HttpCodeResponse(): void
    {
        $payload = [
            'productId' => '1',
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    public function testOnAddProductCartWithZeroInValidRequestPayloadThenReturns422HttpCodeResponse(): void
    {
        $payload = [
            'productId' => 0,
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    public function testOnAddProductCartWithNegativeInValidRequestPayloadThenReturns200HttpCodeResponse(): void
    {
        $payload = [
            'productId' => -1,
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    public function testOnRemoveProductCartWithValidRequestPayloadWhenProductExistInBDThenReturns200HttpCodeResponse(): void
    {
        ProductFactory::new()->create(
            ['name' => 'Test Product', 'price' => 1000]
        );

        $payload = [
            'productId' => 1,
        ];

        $this->getClientRequest($payload, self::REMOVE_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Product removed from cart', $responseDecoded['message']);
    }

    public function testOnRemoveProductCartWithValidRequestPayloadWhenProductNotExistInBDThenReturns400HttpCodeResponse(): void
    {
        $payload = [
            'productId' => 1,
        ];

        $this->getClientRequest($payload, self::REMOVE_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Product with ID 1 not found', $responseDecoded['message']);
    }

    public function testOnRemoveProductCartWithStringInValidRequestPayloadThenReturns422HttpCodeResponse(): void
    {
        $payload = [
            'productId' => '1',
        ];

        $this->getClientRequest($payload, self::REMOVE_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    public function testOnRemoveProductCartWithZeroInValidRequestPayloadThenReturns422HttpCodeResponse(): void
    {
        $payload = [
            'productId' => 0,
        ];

        $this->getClientRequest($payload, self::REMOVE_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    public function testOnRemoveProductCartWithNegativeInValidRequestPayloadThenReturns200HttpCodeResponse(): void
    {
        $payload = [
            'productId' => -1,
        ];

        $this->getClientRequest($payload, self::REMOVE_PRODUCT_CART_ENDPOINT);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame('Validation failed', $responseDecoded['message']);
    }

    // TODO: Add test for update product in cart

    public function testOnViewCartThenReturns200HttpCodeResponse(): void
    {
        $expectedResult = [
            'cartItems' => [
                1 => [
                    'productName' => 'Test Product',
                    'quantity' => [
                        'value' => 1,
                    ],
                    'price' => [
                        'value' => 1000.0,
                    ],
                ],
            ],
            'totalPrice' => 1000.0,
            'empty' => false,
        ];

        ProductFactory::new()->create(
            ['name' => 'Test Product', 'price' => 1000]
        );

        $payload = [
            'productId' => 1,
        ];

        $this->getClientRequest($payload, self::ADD_PRODUCT_CART_ENDPOINT);

        $this->getClientRequest([], self::VIEW_CART_ENDPOINT, 'GET');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseContent = $this->client->getResponse()->getContent();
        $responseDecoded = json_decode($responseContent, true);

        self::assertSame($expectedResult, $responseDecoded);
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
