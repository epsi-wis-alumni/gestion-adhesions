<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Form\AdminNewsletterType;
use App\Repository\MailTemplateRepository;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/newsletter')]
final class AdminNewsletterController extends AbstractController
{
    #[Route(name: 'app_admin_newsletter_index', methods: ['GET'])]
    public function index(NewsletterRepository $newsletterRepository): Response
    {
        return $this->render('admin/newsletter/index.html.twig', [
            'newsletters' => $newsletterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_newsletter_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
    ): Response {

        $newsletter = new Newsletter();
        $form = $this->createForm(AdminNewsletterType::class, $newsletter);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setCreatedBy($currentUser);
            $entityManager->persist($newsletter);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_newsletter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/newsletter/new.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_newsletter_show', methods: ['GET'])]
    public function show(Newsletter $newsletter): Response
    {
        return $this->render('admin/newsletter/show.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_newsletter_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Newsletter $newsletter,
        EntityManagerInterface $entityManager,
        MailTemplateRepository $mailTemplateRepository,
    ): Response {

        $form = $this->createForm(AdminNewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_newsletter_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/newsletter/edit.html.twig', [
            'newsletter' => $newsletter,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_newsletter_delete', methods: ['POST'])]
    public function delete(Request $request, Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$newsletter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($newsletter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_newsletter_index', [], Response::HTTP_SEE_OTHER);
    }
}
