<?php

namespace App\Service;

use App\Entity\Election;
use App\Entity\Event;
use App\Entity\Invoice;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

final readonly class NotificationManager
{
    public function __construct(
        private UserRepository $userRepository,
        private MailerInterface $mailerInterface,
        private ContainerBagInterface $params,
    ) {
    }

    public function send(Election|Event $entity): void
    {
        $users = $this->userRepository->findByNotificationsAllowed();

        $this->sendNotification($entity, $users);
    }

    public function sendNotification(Election|Event|Invoice|User $entity, array $users, bool $newAccount = false): void
    {
        $templateName = match ($entity::class) {
            Election::class => 'mails/election.html.twig',
            Event::class => 'mails/event.html.twig',
            Invoice::class => 'mails/invoice.html.twig',
            User::class => $newAccount ? 'mails/newAccount.html.twig' : 'mails/updateAccount.html.twig',
        };
        $subject = match ($entity::class) {
            Election::class => 'Élection pour '.$entity->getJobTitle(),
            Event::class => 'Nouvel Évènement : '.$entity->getTitle(),
            Invoice::class => 'Nouvelle Facture disponible',
            User::class => $newAccount ? 'Création de compte' : 'Mise à jour du compte',
        };
        $context = match ($entity::class) {
            Election::class => ['election' => $entity],
            Event::class => ['event' => $entity],
            Invoice::class => [
                'type' => TransactionType::Donation,
                'invoice_url' => "",
            ],
            User::class => ['user' => $entity],
        };
        $bcc = match ($entity::class) {
            Election::class => array_filter($users, fn (User $user): ?bool => $user->getSettings()->isElectionNotificationsAllowed()),
            Event::class => array_filter($users, fn (User $user): ?bool => $user->getSettings()->isEventNotificationsAllowed()),
            Invoice::class => $users,
            User::class => $users,
        };
        $filePath = match ($entity::class) {
            Election::class => null,
            Event::class => null,
            Invoice::class => $entity->getFilePath(),
            User::class => null,
        };
        $sender = $this->params->get('mailer_sender');

        $bcc = array_map(fn (User $user): ?string => $user->getEmail(), $bcc);

        if (count($bcc)) {
            $email = (new TemplatedEmail())
                ->from($sender)
                ->bcc(...$bcc)
                ->subject($subject)
                ->htmlTemplate($templateName)
                ->context($context)
            ;
            if ($filePath) {
                $email->addPart(new DataPart(new File($filePath)));
            }

            $this->mailerInterface->send($email);
        }
    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn ($user) => $user->getEmail(), $users);
    }

    public function sendInvoiceLink(string $invoice_url, User $user): void {
        $sender = $this->params->get('mailer_sender');

        if ($user) {
            $email = (new TemplatedEmail())
                ->from($sender)
                ->to($user->getEmail())
                ->subject('Nouvelle Facture disponible')
                ->htmlTemplate('mails/invoice.html.twig')
                ->context([
                    'type' => TransactionType::Subscription,
                    'invoice_url' => $invoice_url,
                ])
            ;

            $this->mailerInterface->send($email);
        }
    }
}
