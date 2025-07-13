<?php

namespace App\Tests\Infrastructure\EventSubscriber;

use App\Infrastructure\EventSubscriber\ValidationExceptionListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidationFailedException;

#[CoversClass(ValidationExceptionListener::class)]
class ValidationExceptionListenerTest extends TestCase
{
    public function testOnKernelExceptionWhenPreviousThrowableIsValidationFailedExceptionThenReturns422HttpCodeResponseAndValidMessagesAndProperties(): void
    {
        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $violation1 = new ConstraintViolation(
            'propertyName1 not valid',
            null,
            [],
            '',
            'propertyName1',
            ''
        );

        $violation2 = new ConstraintViolation(
            'propertyName2 not valid',
            null,
            [],
            '',
            'propertyName2',
            ''
        );

        $constraintViolationList = new ConstraintViolationList([$violation1, $violation2]);
        $validationExceptionListenerPreviousException = new ValidationFailedException(0, $constraintViolationList);

        $throwable = new \Exception('message', 0, $validationExceptionListenerPreviousException);
        $exceptionEvent = new ExceptionEvent($httpKernelInterface, $request, 0, $throwable);

        $validationExceptionListener = new ValidationExceptionListener();
        $validationExceptionListener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $responseDecoded = json_decode($response->getContent(), true);

        $this->assertSame('Validation failed', $responseDecoded['message']);
        $this->assertCount(2, $responseDecoded['errors']);

        $this->assertSame('propertyName1', $responseDecoded['errors'][0]['property']);
        $this->assertSame('propertyName1 not valid', $responseDecoded['errors'][0]['message']);

        $this->assertSame('propertyName2', $responseDecoded['errors'][1]['property']);
        $this->assertSame('propertyName2 not valid', $responseDecoded['errors'][1]['message']);

        $this->assertSame(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
    }

    public function testOnKernelExceptionWhenPreviousThrowableIsNotEncodableValueExceptionThenReturns422HttpCodeResponseAndValidMessage(): void
    {
        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $previousException = new NotEncodableValueException('hola', 0);
        $throwable = new \Exception('', 0, $previousException);

        $exceptionEvent = new ExceptionEvent($httpKernelInterface, $request, 0, $throwable);

        $validationExceptionListener = new ValidationExceptionListener();
        $validationExceptionListener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $responseDecoded = json_decode($response->getContent(), true);

        $this->assertSame('request not encodable value', $responseDecoded['message']);
        $this->assertSame(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testOnKernelExceptionWhenPreviousThrowableHasNotExceptionThenReturns500HttpCodeResponseAndValidMessage(): void
    {
        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $throwable = new \Exception('some error message', 0);

        $exceptionEvent = new ExceptionEvent($httpKernelInterface, $request, 0, $throwable);

        $validationExceptionListener = new ValidationExceptionListener();
        $validationExceptionListener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $responseDecoded = json_decode($response->getContent(), true);

        $this->assertSame('some error message', $responseDecoded['message']);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }

    public function testOnKernelExceptionWhenPreviousThrowableIsDifferentToValidationFailedExceptionAndNotEncodableValueExceptionThenReturns500HttpCodeResponseAndValidMessage(): void
    {
        $httpKernelInterface = $this->createMock(HttpKernelInterface::class);
        $request = new Request();

        $throwable = new \Exception('some error message', 0);

        $exceptionEvent = new ExceptionEvent($httpKernelInterface, $request, 0, $throwable);

        $validationExceptionListener = new ValidationExceptionListener();
        $validationExceptionListener->onKernelException($exceptionEvent);

        $response = $exceptionEvent->getResponse();

        $responseDecoded = json_decode($response->getContent(), true);

        $this->assertSame('some error message', $responseDecoded['message']);
        $this->assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
    }
}
