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

        $entityName = $this->getEntityName($entity);
        $email = (new TemplatedEmail())
            ->from('test@epsi-wis-alumni.fr')
            ->bcc(...$users);

        switch ($entityName) {
            case 'election':
                $email->subject('Élection pour ' . $entity->getJobTitle());
                break;
            case 'event':
                $email->subject('Nouvel Événement : ' . $entity->getTitle());
                break;
            default:
                throw new \InvalidArgumentException("Type d'entité non pris en charge : " . $entityName);
        };

        $email
            ->htmlTemplate('mails/' . $entityName . '.html.twig')
            ->context([
                $entityName => $entity
            ])
        ;

        $this->mailerInterface->send($email);
    }

    public function getMailFromUsers(array $users): array
    {
        return array_map(fn($user) => $user->getEmail(), $users);
    }

    public function getEntityName(object $entity): string
    {
        return strtolower((new \ReflectionClass($entity))->getShortName());
    }
}
