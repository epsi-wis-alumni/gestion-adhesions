<?php

namespace App\Repository;

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
    public function hasVoted($user, $election): bool
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.voter = :user')
            ->andWhere('v.election = :election')
            ->setParameter('user', $user)
            ->setParameter('election', $election)
            ->getQuery()
            ->getOneOrNullResult() ? true : false;;
    }

    /**
     * @return int Returns number of vote for a candidate
     */
    public function getNbVote(int $candidateId, int $electionId): int
    {
        return $this->createQueryBuilder('v')
            ->select('COUNT(v.id) as NB')   
            ->leftJoin('v.candidate', 'c')
            ->leftJoin('v.election', 'e')
            ->andWhere('c.id = :candidateId')
            ->andWhere('e.id = :electionId')
            ->setParameter('candidateId', $candidateId)
            ->setParameter('electionId', $electionId)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    //    public function findOneBySomeField($value): ?Vote
    //    {
    //        return $this->createQueryBuilder('v')
    //            ->andWhere('v.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
