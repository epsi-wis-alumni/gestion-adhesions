<?php

namespace App\Service;

use App\Entity\User;
use DateTimeImmutable;

final class UserManager
{
    public function approve(User $who, User $by): void
    {
        $who
            ->setRejectedBy(null)
            ->setRejectedAt(null)
            ->setApprovedBy($by)
            ->setApprovedAt(new DateTimeImmutable())
        ;
    }
    
    
    public function reject(User $who, User $by): void
    {
        $who
            ->setApprovedBy(null)
            ->setApprovedAt(null)
            ->setRejectedBy($by)
            ->setRejectedAt(new DateTimeImmutable())
        ;
    }
}
