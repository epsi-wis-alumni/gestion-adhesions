<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;

final readonly class ContactManager
{
    public function __construct(
        private MailerInterface $mailerInterface,
        private ContainerBagInterface $params,
    ) {
    }

    public function sendMail($form): void 
    {
        $sender = $this->params->get('mailer_sender');
        $contactAddress = $this->params->get('mailer_contact');

        $templatePath = 'mails/contact.html.twig';

        $email = (new TemplatedEmail())
            ->from($sender)
            ->to($contactAddress)
            ->subject("Prise de contact depuis le site")
            ->htmlTemplate($templatePath)
            ->context([
                'form' => $form,
            ]);

        $this->mailerInterface->send($email);
    }
}
