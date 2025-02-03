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

    public function sendElectionNotification(Election $election): void
    {
        $users = $this->userRepository->findByAllowNotifications();

        $this->sendNotification($election, $this->getMailFromUsers($users));

        // $this->entityManager->flush();
    }

    public function sendNotification(Election $election, array $users): void
    {
        $email = (new TemplatedEmail())
            ->from('test@epsi-wis-alumni.fr')
            ->to('test+to@epsi-wis-alumni.fr')
            // ->bcc(implode("','", $users))
            ->bcc('aurelienlol33@gmail.com','test+bcc@epsi-wis-alumni.fr')
            ->subject('Élection pour ' . $election->getJobTitle())
            ->htmlTemplate('mails/test.html.twig')
            ->context([
                'election' => $election,
            ]);

        try {
            $this->mailerInterface->send($email);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn($user) => $user->getEmail(), $users);
    }
}
