<?php

namespace App\Service;

use App\Entity\Candidate;
use App\Entity\Election;
use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;

final class ElectionManager
{

    public function candidate(User $who, Candidate $what, Election $for): void
    {
        $what
            ->setElection($for)
            ->setCandidate($who)
        ;
    }
}
