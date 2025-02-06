<?php

namespace App\Service;

use App\Entity\Election;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class NotificationManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private MailerInterface $mailerInterface,
    ) {}

    public function send(object $entity): void
    {
        $users = $this->userRepository->findByNotificationsAllowed();

        $this->sendNotification($entity, $this->getMailFromUsers($users));
    }

    public function sendNotification(object $entity, array $users): void
    {
        $email = (new TemplatedEmail())
            ->from('test@epsi-wis-alumni.fr')
            ->bcc(...$users)
            ->subject('Élection pour ' . $election->getJobTitle())
            ->htmlTemplate('mails/election.html.twig')
            ->context([
                'election' => $election,
            ]);

        $this->mailerInterface->send($email);
    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn($user) => $user->getEmail(), $users);
    }
}
