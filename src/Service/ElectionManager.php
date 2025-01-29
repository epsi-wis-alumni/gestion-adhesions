<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\CandidateRepository;
use App\Repository\VoteRepository;
use DateTimeImmutable;
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

    /**
     * @return
     */
    public function getWinners(Election $election): array
    {
        $votes = $election->getVotes();
        $voteCount = $votes->count();
        $results = $this->candidateRepository->findByVoteCount($election);

        $maxVoteCount = $this->getMaxVoteCount($results);

        return array_filter(
            $results,
            fn (Candidate $candidate) => $candidate->getVotes()->count() === $maxVoteCount
        );
    }

    protected function getMaxVoteCount(array $candidates): int
    {
        return count($candidates) > 0 ? max(
            array_map(fn (Candidate $candidate) => $candidate->getVotes()->count(), $candidates)
        ) : 0;
    }

    public function getStep(Election $election): int
    {
        $now = new DateTimeImmutable();
        
        return $election->getVoteStartAt() > $now
            ? 1 : ($election->getVoteStartAt() < $now && $election->getVoteEndAt() > $now
            ? 2 : 3)
        ;
    }
}
