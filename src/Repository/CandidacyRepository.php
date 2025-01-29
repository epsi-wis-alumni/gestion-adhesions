<?php

namespace App\Repository;

use App\Entity\Candidacy;
use App\Entity\Election;
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
}
