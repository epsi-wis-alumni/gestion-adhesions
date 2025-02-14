<?php

namespace App\Repository;

use App\Entity\Plan;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Plan>
 */
class PlanRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry,
        private UserRepository $userRepository,
    ) {
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

    /**
     * @return ?plan Returns a plan or null
     */
    public function getPlanByUser(User $user): ?Plan
    {
        $planId = $this->userRepository->findActivePlanIdByUser($user);
        return $planId ? $this->find($planId) : null;
    }
}
