<?php

namespace App\Service;

use App\Entity\Election;
use App\Entity\Event;
use App\Entity\Invoice;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\File;

final class NotificationManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private MailerInterface $mailerInterface,
        private ContainerBagInterface $params,
    ) {}

    public function send(Election|Event $entity): void
    {
        $users = $this->userRepository->findByNotificationsAllowed();

        $this->sendNotification($entity, $users);
    }

    public function sendNotification(Election|Event|Invoice $entity, array $users): void
    {
        $templateName = match($entity::class) {
            Election::class => 'mails/election.html.twig',
            Event::class => 'mails/event.html.twig',
            Invoice::class => 'mails/invoice.html.twig'
        };
        $subject = match($entity::class) {
            Election::class => 'Élection pour ' . $entity->getJobTitle(),
            Event::class => 'Nouvel Évènement : ' . $entity->getTitle(),
            Invoice::class => 'Nouvelle Facture disponible',
        };
        $context = match($entity::class) {
            Election::class => ['election' => $entity],
            Event::class => ['event' => $entity],
            Invoice::class => [],
        };
        $bcc = match ($entity::class) {
            Election::class => array_filter($users, fn (User $user) => $user->getSettings()->isElectionNotificationsAllowed()),
            Event::class => array_filter($users, fn (User $user) => $user->getSettings()->isEventNotificationsAllowed()),
            Invoice::class => $users,
        };
        $filePath = match ($entity::class) {
            Election::class => null,
            Event::class => null,
            Invoice::class => $entity->getFilePath(),
        };
        $sender = $this->params->get('mailer_sender');

        $bcc = array_map(fn (User $user) => $user->getEmail(), $bcc);

        if (count($bcc)) {
            $email = (new TemplatedEmail())
                ->from($sender)
                ->bcc(...$bcc)
                ->subject($subject)
                ->htmlTemplate($templateName)
                ->context($context)
            ;
            if($filePath) {
                $email->addPart(new DataPart(new File($filePath)));
            }

            $this->mailerInterface->send($email);
        }

    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn($user) => $user->getEmail(), $users);
    }
}
