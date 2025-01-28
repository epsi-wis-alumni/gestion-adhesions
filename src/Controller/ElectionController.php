<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Form\CandidateType;
use App\Repository\CandidateRepository;
use App\Repository\ElectionRepository;
use App\Service\ElectionManager;
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
        CandidateRepository $candidateRepository,
    ): Response {
        $votes = $election->getVotes();
        $voteCount = $votes->count();
        $results = $candidateRepository->findByVoteCount($election);

        $maxVoteCount = max(
            array_map(fn (Candidate $candidate) => $candidate->getVotes()->count(), $results)
        );

        $winners = array_filter(
            $results,
            fn (Candidate $candidate) => $candidate->getVotes()->count() === $maxVoteCount
        );
        
        return $this->render('election/show.html.twig', [
            'voteCount' => $voteCount,
            'results' => $results,
            'winners' => $winners,
            'election' => $election,
            'candidates' => $election->getCandidates(),
        ]);
    }

    #[Route('/{id}/candidate', name: 'app_election_candidate', methods: ['GET', 'POST'])]
    public function candidate(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
        ElectionManager $electionManager,
        Election $election
    ): Response {

        $candidate = new Candidate();
        $form = $this->createForm(CandidateType::class, $candidate);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $electionManager->candidate(user: $currentUser, candidate: $candidate, election: $election);
            $entityManager->persist($candidate);
            $entityManager->flush();

            return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('election/candidate.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{electionId}/vote/{candidateId}', name: 'app_election_vote', methods: ['GET'])]
    public function vote(
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
        #[MapEntity(id: 'election_id')] Election $election,
        #[MapEntity(id: 'candidate_id')] Candidate $candidate,
        ElectionManager $electionManager
    ): Response {

        $vote = new Vote();
        $electionManager->vote(user: $currentUser, vote: $vote, candidate: $candidate, election: $election);
        $entityManager->persist($vote);
        $entityManager->flush();

        return $this->redirectToRoute('app_election_index', [], Response::HTTP_SEE_OTHER);
    }
}
