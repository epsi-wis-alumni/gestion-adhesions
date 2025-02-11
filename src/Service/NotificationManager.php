<?php

namespace App\Service;

use App\Entity\Election;
use App\Entity\Event;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

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

    public function sendNotification(Election|Event $entity, array $users): void
    {
        $templateName = match($entity::class) {
            Election::class => 'mails/election.html.twig',
            Event::class => 'mails/event.html.twig',
        };
        $subject = match($entity::class) {
            Election::class => 'Élection pour ' . $entity->getJobTitle(),
            Event::class => 'Nouvel Évènement : ' . $entity->getTitle(),
        };
        $context = match($entity::class) {
            Election::class => ['election' => $entity],
            Event::class => ['event' => $entity],
        };
        $bcc = match ($entity::class) {
            Election::class => array_filter($users, fn (User $user) => $user->getSettings()->isElectionNotificationsAllowed()),
            Event::class => array_filter($users, fn (User $user) => $user->getSettings()->isEventNotificationsAllowed()),
        };
        $sender = $this->params->get('mailer_sender');

        $email = (new TemplatedEmail())
            ->from($sender)
            ->bcc(...array_map(fn (User $user) => $user->getEmail(), $bcc))
            ->subject($subject)
            ->htmlTemplate($templateName)
            ->context($context)
        ;

        $this->mailerInterface->send($email);
    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn($user) => $user->getEmail(), $users);
    }
}
