<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/newsletter')]
class NewsletterController extends AbstractController
{
    #[Route('/{id}', name: 'app_newsletter_show')]
    public function index(
        Newsletter $newsletter,
        NewsletterRepository $newsletterRepository,
    ): Response {

        $currentNewsletter = $newsletterRepository->find($newsletter);
        $templateFileName = $currentNewsletter->getTemplate()->getFileName();

        if (!file_exists(realpath(__DIR__ . '/../../templates/mails/' . $templateFileName))) {
            $userNewsletter = 'userNewsletter';
        }

        return $this->render('mails/' . $templateFileName, [
            'userNewsletter' => $userNewsletter,
        ]);
    }
}
