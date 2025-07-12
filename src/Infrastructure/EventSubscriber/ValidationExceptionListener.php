<?php

namespace App\Infrastructure\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ValidationExceptionListener implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $previousException = $exception->getPrevious();

        if ($previousException instanceof ValidationFailedException) {
            $this->handleValidationFailedException($event, $previousException);

            return;
        }

        if ($previousException instanceof NotEncodableValueException) {
            $response = new JsonResponse([
                'message' => 'request not encodable value',
            ], Response::HTTP_BAD_REQUEST);

            $event->setResponse($response);
            $event->stopPropagation();

            return;
        }

        $response = new JsonResponse([
            'message' => $exception->getMessage(),
        ], Response::HTTP_INTERNAL_SERVER_ERROR);

        $event->setResponse($response);
    }

    private function handleValidationFailedException(ExceptionEvent $event, ValidationFailedException $exception): void
    {
        $violations = $exception->getViolations();
        $errors = $this->formatViolations($violations);

        $response = new JsonResponse([
            'message' => 'Validation failed',
            'errors' => $errors,
        ], Response::HTTP_UNPROCESSABLE_ENTITY);

        $event->setResponse($response);
        $event->stopPropagation();
    }

    private function formatViolations(ConstraintViolationListInterface $violations): array
    {
        $errors = [];
        foreach ($violations as $violation) {
            $errors[] = [
                'property' => $violation->getPropertyPath(),
                'message' => $violation->getMessage(),
            ];
        }

        return $errors;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }
}
