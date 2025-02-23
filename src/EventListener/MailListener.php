<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Mailer\Event\FailedMessageEvent;
use Symfony\Component\Mailer\Event\MessageEvent;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class MailListener
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    #[AsEventListener(event: MessageEvent::class)]
    public function onMessageEvent(MessageEvent $event): void
    {
        $message = $event->getMessage();

        if ($message instanceof \Symfony\Component\Mime\Email) {
            $to = $message->getTo();
            $cc = $message->getCc();
            $bcc = $message->getBcc();

            $addresses = array_merge(
                array_map(function ($address) {
                    return $address->getAddress();
                }, $to),
                array_map(function ($address) {
                    return $address->getAddress();
                }, $cc),
                array_map(function ($address) {
                    return $address->getAddress();
                }, $bcc)
            );

            $this->logger->info('Un e-mail a été envoyé à : '.implode(', ', $addresses));
        }
    }

    #[AsEventListener(event: FailedMessageEvent::class)]
    public function onFailedMessageEvent(FailedMessageEvent $event): void
    {
        $error = $event->getError();
        if ($error instanceof TransportExceptionInterface) {
            $this->logger->debug('Erreur lors de l\'envoi de l\'e-mail', [
                'message' => $error->getMessage(),
            ]);
        }
    }
}
