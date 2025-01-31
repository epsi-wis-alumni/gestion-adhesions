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
        
        $this->sendMail($election, $this->getMailingListFromUsers($users));

        $this->entityManager->flush();
    }

    public function sendMail(Election $election, array $user): void
    {
        // $email = (new TemplatedEmail())
        //     ->from('test@epsi-wis-alumni.fr')
        //     ->bcc(join(',', $user))
        //     ->subject('Élection pour ' . $election->getJobTitle())
        //     ->htmlTemplate('mails/test.html.twig')
        //     ->context([
        //         'election' => $election,
        //     ]);
        
        dd(join(',', $user));
        try {
            $this->mailerInterface->send($email);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function getMailingListFromUsers(array $users): array
    {
        return array_map(fn ($user) => $user->getEmail(), $users);
    }
}

