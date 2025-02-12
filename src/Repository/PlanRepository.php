<?php

namespace App\Repository;

use App\Entity\Plan;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plan>
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Plan::class);
    }

    /**
     * Définit tous les plans comme non mis en avant (highlighted = false)
     */
    public function resetAllHighlighted(): void
    {
        $this->createQueryBuilder('p')
            ->update()
            ->set('p.highlighted', ':highlighted')
            ->setParameter('highlighted', false)
            ->getQuery()
            ->execute();
    }
}
