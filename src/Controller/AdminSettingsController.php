<?php

namespace App\Controller;

use App\Entity\MailTemplate;
use App\Form\MailTemplateType;
use App\Repository\MailTemplateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/settings')]
final class AdminSettingsController extends AbstractController
{
    #[Route('/', name: 'app_admin_settings')]
    public function index(MailTemplateRepository $mailTemplateRepository): Response
    {
        return $this->render('admin/settings/index.html.twig', [
            'mailTemplates' => $mailTemplateRepository->findAll(),
        ]);
    }

    #[Route('/empty-cache', name: 'app_admin_settings_empty_cache')]
    public function emptyCache(): Response
    {
        opcache_reset();
        
        $this->addFlash('success', 'Le cache a bien été vidé.');

        return $this->redirectToRoute('app_admin_settings');
    }

    #[Route('/mail-template/new', name: 'app_admin_settings_mailtemplate_new', methods: ['GET', 'POST'])]
    public function newMailTemplate(Request $request, EntityManagerInterface $entityManager)
    {
        $mailTemplate = new MailTemplate();
        $form = $this->createForm(MailTemplateType::class, $mailTemplate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mailTemplate);
            $entityManager->flush();

            $this->addFlash('success', 'Le modèle a bien été créé.');

            return $this->redirectToRoute('app_admin_settings', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/admin/mail-template/new.html.twig', [
            'form' => $form,
        ]);
    }
}
