<?php

namespace App\Repository;

use App\Entity\Election;
use App\Entity\User;
use App\Entity\Vote;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Vote>
 */
class VoteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }

    /**
     * @return boolean Returns a boolean
     */
    public function hasVoted(User $user, Election $election): bool
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.voter = :user')
            ->andWhere('v.election = :election')
            ->setParameter('user', $user)
            ->setParameter('election', $election)
            ->getQuery()
            ->getOneOrNullResult() ? true : false;;
    }
}
