<?php

namespace App\Service;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\CandidacyRepository;
use App\Repository\VoteRepository;

final class ElectionManager
{
    public function __construct(
        protected VoteRepository $voteRepository,
        protected CandidacyRepository $candidacyRepository,
    ) {
    }

    public function create(User $user, Election $election): void
    {
        $election
            ->setCreatedBy($user)
        ;
    }

    public function update(User $user, Election $election): void
    {
        $election
            ->setUpdatedBy($user)
        ;
    }

    public function candidate(User $user, Candidacy $candidacy, Election $election): void
    {
        $candidacy
            ->setCandidate($user)
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
     * @return list<Candidacy>
     */
    public function getWinners(Election $election): array
    {
        $results = $this->candidacyRepository->findByVoteCount($election);
        $maxVoteCount = $this->getMaxVoteCount($results);

        return array_filter(
            $results,
            fn (Candidacy $candidacy): bool => $candidacy->getVotes()->count() === $maxVoteCount
        );
    }

    /**
     * @param list<Candidacy> $candidacies
     */
    protected function getMaxVoteCount(array $candidacies): int
    {
        return count($candidacies) > 0 ? max(
            array_map(fn (Candidacy $candidacy) => $candidacy->getVotes()->count(), $candidacies)
        ) : 0;
    }

    public function getStep(Election $election): int
    {
        $now = new \DateTimeImmutable();

        return $election->getVoteStartAt() > $now
            ? 1 : ($election->getVoteStartAt() < $now && $election->getVoteEndAt() > $now
            ? 2 : 3)
        ;
    }
}
