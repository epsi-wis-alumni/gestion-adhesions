<?php

namespace App\Service;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\CandidacyRepository;
use App\Repository\VoteRepository;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;

final class ElectionManager
{
    public function __construct(
        protected VoteRepository $voteRepository,
        protected CandidacyRepository $candidacyRepository,
    ) {}

    public function candidacy(User $user, Candidacy $candidacy, Election $election): void
    {
        $candidacy
            ->setCandidacy($user)
            ->setElection($election)
        ;
    }

    public function vote(User $user, Vote $vote, Candidacy $candidacy, Election $election): void
    {
        $vote
            ->setVoter($user)
            ->setCandidacy($candidacy)
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
        $results = $this->candidacyRepository->findByVoteCount($election);

        $maxVoteCount = $this->getMaxVoteCount($results);

        return array_filter(
            $results,
            fn (Candidacy $candidacy) => $candidacy->getVotes()->count() === $maxVoteCount
        );
    }

    protected function getMaxVoteCount(array $candidacys): int
    {
        return count($candidacys) > 0 ? max(
            array_map(fn (Candidacy $candidacy) => $candidacy->getVotes()->count(), $candidacys)
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
