<?php

namespace App\Controller;

use App\Entity\Election;
use App\Entity\User;
use App\Form\AdminElectionType;
use App\Repository\ElectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/election')]
final class AdminElectionController extends AbstractController
{
    #[Route(name: 'app_admin_election_index', methods: ['GET'])]
    public function index(ElectionRepository $electionRepository): Response
    {
        return $this->render('admin/election/index.html.twig', [
            'elections' => $electionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_election_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser): Response
    {
        $election = new Election();
        $form = $this->createForm(AdminElectionType::class, $election);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $election->setCreatedBy($currentUser);
            $entityManager->persist($election);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/election/new.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_admin_election_show', methods: ['GET'])]
    public function show(Election $election): Response
    {
        return $this->render('admin/election/show.html.twig', [
            'election' => $election,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_election_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminElectionType::class, $election);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/election/edit.html.twig', [
            'election' => $election,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_election_delete', methods: ['POST'])]
    public function delete(Request $request, Election $election, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$election->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($election);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_election_index', [], Response::HTTP_SEE_OTHER);
    }
}
