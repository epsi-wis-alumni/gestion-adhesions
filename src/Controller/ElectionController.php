<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Repository\ElectionRepository;
use App\Service\ElectionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/election')]
class ElectionController extends AbstractController
{
    #[Route(name: 'app_election_index', methods: ['GET'])]
    public function index(ElectionRepository $electionRepository): Response
    {
        return $this->render('election/index.html.twig', [
            'elections' => $electionRepository->findAll(),
        ]);
    }

    #[Route('/{id}/candidate', name: 'app_candidate', methods: ['GET'])]
    public function candidate(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, ElectionManager $electionManager, Election $election): Response
    {
        $candidate = new Candidate();
        $electionManager->candidate(user: $currentUser, candidate: $candidate, election: $election);
        $entityManager->persist($candidate);
        $entityManager->flush();

        return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);

    }
}
