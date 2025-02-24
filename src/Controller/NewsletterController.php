<?php

namespace App\Controller;

use App\Entity\UserNewsletter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
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

        if (!file_exists(realpath(__DIR__.$params->get('mails_base_path').$templateFileName))) {
            $userNewsletter = 'userNewsletter';
        }

        return $this->render('mails/'.$templateFileName, [
            'userNewsletter' => $userNewsletter,
        ]);
    }
}
