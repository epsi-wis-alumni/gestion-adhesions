<?php

namespace App\Service;

use App\Entity\Newsletter;
use App\Entity\UserNewsletter;
use App\Repository\UserNewsletterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

final class NewsletterManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserNewsletterRepository $userNewsletterRepository,
        private MailerInterface $mailerInterface,
    ) {
    }

    public function send(Newsletter $newsletter): void
    {
        // On lie les users et la newsletter et on génère les Uuid (token :) )
        $this->prepareUserNewsletters($newsletter);

        // On récupère les liens pour avoir toutes les informations pour le template
        $userNewsletters = $this->userNewsletterRepository->findByNewsletter($newsletter);

        // On envoie tous les mails
        foreach ($userNewsletters as $key => $userNewsletter) {
            $this->sendMail($userNewsletter);
        }
    }

    public function prepareUserNewsletters(Newsletter $newsletter): void
    {
        $users = $this->userRepository->findByAllowNewsletter();

        foreach ($users as $key => $user) {
            $userNewsletter = new UserNewsletter();
            $userNewsletter
                ->setNewsletter($newsletter)
                ->setUser($user)
            ;
            $this->entityManager->persist($userNewsletter);

            if (!(($key + 1) % 80)) {
                $this->entityManager->flush();
            }
        }
        $this->entityManager->flush();
    }

    public function sendMail(
        $userNewsletter,
    ): void {
        $email = (new TemplatedEmail())
            // ->from('test@epsi-wis-alumni.fr')
            ->to($userNewsletter->getUser()->getEmail())
            ->subject($userNewsletter->getNewsletter()->getObject())
            ->htmlTemplate('mails/' . ($userNewsletter->getNewsletter()->getTemplate()->getFileName()))
            ->context([
                'userNewsletter' => $userNewsletter,
            ]);
        dd($this->mailerInterface->send($email)->getDebug());
    }
}
