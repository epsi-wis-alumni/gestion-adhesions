<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;

final class ElectionManager
{

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
}
