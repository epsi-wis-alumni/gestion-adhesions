<?php

namespace App\Repository;

use App\Entity\Candidacy;
use App\Entity\Election;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Candidacy>
 */
class CandidacyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Candidacy::class);
    }

    /**
     * @return Candidacy[]
     */
    public function findByVoteCount(Election $election): array
    {
        $qb = $this->createQueryBuilder('c')
            ->leftJoin('c.votes', 'v')
            ->leftJoin('c.election', 'e')
            ->where('e.id = :electionId')
            ->groupBy('c.id')
            ->orderBy('COUNT(v.id)', 'DESC')
            ->setParameter('electionId', $election->getId())
        ;

        return $qb->getQuery()->getResult();
    }

    /**
     * @return bool Returns a boolean
     */
    public function hasCandidated(User $user, Election $election): bool
    {
        return (bool) $this->createQueryBuilder('c')
            ->andWhere('c.candidate = :candidate')
            ->andWhere('c.election = :election')
            ->setParameter('candidate', $user)
            ->setParameter('election', $election)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
