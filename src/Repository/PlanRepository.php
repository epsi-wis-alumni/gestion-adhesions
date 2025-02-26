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
     * Définit tous les plans comme non mis en avant (highlighted = false).
     */
    public function resetAllHighlighted(?Plan $ignored = null): void
    {
        $qb = $this->createQueryBuilder('p')
            ->update()
            ->set('p.highlighted', ':highlighted')
            ->setParameter('highlighted', false)
        ;

        if ($ignored) {
            $qb
                ->where('p.id <> :planId')
                ->setParameter('planId', $ignored->getId())
            ;
        }

        $qb->getQuery()
            ->execute();
    }

    public function findOneActivePlanByUser(User $user): ?Plan
    {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.subscriptions', 's')
            ->leftJoin('s.transactions', 't')
            ->leftJoin('t.user', 'u')
            ->where('s.plan = p')
            ->where('t.subscription = s')
            ->where('t.user = :user')
            ->andWhere('t.createdAt >= :date')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Plan[] orderby highlight and price
     */
    public function findAllSorted(): array
    {
        $plans = $this->createQueryBuilder('p')
            ->orderBy('p.highlighted', 'DESC')
            ->addOrderBy('p.price', 'ASC')
            ->getQuery()
            ->getResult();

        [$plans[0], $plans[1]] = [$plans[1], $plans[0]];

        return $plans;
    }
}
