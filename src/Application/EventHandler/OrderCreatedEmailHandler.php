<?php

declare(strict_types=1);

namespace App\Application\EventHandler;

use App\Domain\Event\OrderCreatedEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Mime\Email;

#[AsMessageHandler]
final class OrderCreatedEmailHandler
{
    private MailerInterface $mailer;
    private LoggerInterface $logger;

    private string $defaultEmailFrom;

    private string $defaultEmailTo;

    public function __construct(
        MailerInterface $mailer,
        ?LoggerInterface $logger,
        string $defaultEmailFrom,
        string $defaultEmailTo,
    ) {
        $this->mailer = $mailer;
        $this->logger = $logger;
        $this->defaultEmailFrom = $defaultEmailFrom;
        $this->defaultEmailTo = $defaultEmailTo;
    }

    public function __invoke(OrderCreatedEvent $event): void
    {
        try {
            $email = (new Email())
                ->from($this->defaultEmailFrom)
                ->to($this->defaultEmailTo)
                ->subject('¡se ha confirmado un pedido!')
                ->html(
                    '<p>Hola,</p>'.
                    '<p>¡Por favor, hay que despachar el pedido! '.$event->getOrderId().'</p>'
                );

            $this->mailer->send($email);
        } catch (\Exception $e) {
            $this->logger?->error(sprintf('Error al enviar correo para el pedido %d: %s',
                $event->getOrderId(), $e->getMessage()), ['exception' => $e]);
        }
    }
}
