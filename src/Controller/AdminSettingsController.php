<?php

namespace App\Controller;

use App\Entity\MailTemplate;
use App\Form\MailTemplateType;
use App\Repository\MailTemplateRepository;
use App\Service\MailTemplateManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/settings')]
final class AdminSettingsController extends AbstractController
{
    #[Route('/', name: 'app_admin_settings')]
    public function index(MailTemplateRepository $mailTemplateRepository, MailTemplateManager $mailTemplateManager): Response
    {
        return $this->render('admin/settings/index.html.twig', [
            'mailTemplates' => $mailTemplateRepository->findBy(['deleted' => false]),
            'unusedTemplates' => $mailTemplateManager->getUnusedTemplateFiles(),
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
    public function newMailTemplate(Request $request, EntityManagerInterface $entityManager, MailTemplateManager $mailTemplateManager): Response
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
            'unusedTemplates' => $mailTemplateManager->getUnusedTemplateFiles(),
        ]);
    }

    #[Route('/mail-template/{id}/edit', name: 'app_admin_settings_mailtemplate_edit', methods: ['GET', 'POST'])]
    public function editMailTemplate(MailTemplate $mailTemplate, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MailTemplateType::class, $mailTemplate);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le modèle a bien été modifié.');

            return $this->redirectToRoute('app_admin_settings', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('/admin/mail-template/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/mail-template/{id}/delete', name: 'app_admin_settings_mailtemplate_delete', methods: ['POST'])]
    public function deleteMailTemplate(MailTemplate $mailTemplate, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mailTemplate->getId(), $request->getPayload()->getString('_token'))) {
            $mailTemplate->setDeleted(true);
            $entityManager->flush();

            $this->addFlash('success', 'Le modèle a bien été supprimé.');
        }

        return $this->redirectToRoute('app_admin_settings', [], Response::HTTP_SEE_OTHER);
    }
}
