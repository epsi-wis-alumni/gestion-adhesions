<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\CandidateRepository;
use App\Repository\VoteRepository;
use Psr\Log\LoggerInterface;

final class ElectionManager
{
    public function __construct(
        protected VoteRepository $voteRepository,
        protected CandidateRepository $candidateRepository,
    ) {}

    public function candidate(User $user, Candidate $candidate, Election $election): void
    {
        $candidate
            ->setCandidate($user)
            ->setElection($election)
        ;
    }

    public function vote(User $user, Vote $vote, Candidate $candidate, Election $election): void
    {
        $vote
            ->setVoter($user)
            ->setCandidate($candidate)
            ->setElection($election)
        ;
    }

    public function getWinner(Election $election): Candidate
    {
        $candidates = $this->candidateRepository->findByVotes($election);

        return $candidates[0];
    }
}
