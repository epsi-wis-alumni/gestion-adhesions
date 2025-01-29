<?php

namespace App\Controller;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Form\CandidacyType;
use App\Repository\CandidacyRepository;
use App\Repository\ElectionRepository;
use App\Service\ElectionManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/election')]
class ElectionController extends AbstractController
{
    #[Route(name: 'app_election_index', methods: ['GET'])]
    public function index(ElectionRepository $electionRepository): Response
    {
        $pendingElections = $electionRepository->findPending();
        $inProgressElections = $electionRepository->findInProgress();
        $doneElections = $electionRepository->findDone();

        return $this->render('election/index.html.twig', [
            'isClose' => false,
            'pending_elections' => $pendingElections,
            'in_progress_elections' => $inProgressElections,
            'done_elections' => $doneElections,
        ]);
    }

    #[Route('/{id}/show', name: 'app_election_show', methods: ['GET'])]
    public function show(
        Election $election,
        CandidacyRepository $candidacyRepository,
        ElectionManager $electionManager,
    ): Response {
        $voteCount = $election->getVotes()->count();
        $results = $candidacyRepository->findByVoteCount($election);
        $step = $electionManager->getStep($election);
        $winners = $electionManager->getWinners($election);
        $candidacys = $election->getCandidacys();
        
        return $this->render('election/show.html.twig', [
            'voteCount' => $voteCount,
            'results' => $results,
            'winners' => $winners,
            'election' => $election,
            'candidacys' => $candidacys,
            'step' => $step,
        ]);
    }

    #[Route('/{id}/candidacy', name: 'app_election_candidacy', methods: ['GET', 'POST'])]
    public function candidacy(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
        ElectionManager $electionManager,
        Election $election
    ): Response {

        $candidacy = new Candidacy();
        $form = $this->createForm(CandidacyType::class, $candidacy);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $electionManager->candidacy(user: $currentUser, candidacy: $candidacy, election: $election);
            $entityManager->persist($candidacy);
            $entityManager->flush();

            return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/candidacy.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{electionId}/vote/{candidacyId}', name: 'app_election_vote', methods: ['GET'])]
    public function vote(
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
        #[MapEntity(id: 'electionId')] Election $election,
        #[MapEntity(id: 'candidacyId')] Candidacy $candidacy,
        ElectionManager $electionManager
    ): Response {

        $vote = new Vote();
        $electionManager->vote(user: $currentUser, vote: $vote, candidacy: $candidacy, election: $election);
        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
    }
}
