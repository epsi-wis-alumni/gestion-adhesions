<?php

namespace App\Controller;

use App\Entity\UserNewsletter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/newsletter')]
class NewsletterController extends AbstractController
{
    #[Route('/{id}', name: 'app_newsletter_show', methods: ['GET'])]
    public function index(
        UserNewsletter $userNewsletter,
        ContainerBagInterface $params,
    ): Response {
        $templateFileName = $userNewsletter->getNewsletter()->getTemplate()->getFileName();
        $basePath = realpath(__DIR__ . $params->get('mails_base_path'));

        $finder = new Finder();
        $finder->files()->in($basePath)->name($templateFileName);

        if (!$finder->hasResults()) {
            throw $this->createNotFoundException('Le modèle de mail demandé est introuvable.');
        }

        return $this->render('mails/' . $templateFileName, [
            'userNewsletter' => $userNewsletter,
        ]);
    }
}
