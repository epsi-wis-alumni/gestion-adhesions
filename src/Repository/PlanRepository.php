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

    public function findOneActivePlanByUser(User $user): ?Plan
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.subscriptions', 's')
            ->leftJoin('s.transactions', 't')
            ->leftJoin('t.user', 'u')
            ->where('s.plan = p')
            ->where('t.subscription = t')
            ->where('t.user = :user')
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
