<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;

final class ElectionManager
{

    public function candidate(User $user, Candidate $candidate, Election $election): void
    {
        $candidate
            ->setCandidate($user)
            ->setElection($election)
        ;
    }
}
