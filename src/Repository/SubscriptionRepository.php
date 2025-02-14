<?php

namespace App\Repository;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscription>
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @return ?int Returns planId or null
     */
    public function findSubscriptionIdByTransaction(Transaction $transaction): ?int
    {
        $planId = $this->createQueryBuilder('u')
            ->select('s.id')
            ->leftJoin('u.transactions', 't')
            ->leftJoin('t.subscription', 's')
            ->where('t = :transaction')
            ->setParameter('transaction', $transaction)
            ->orderBy('t.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        return $planId ? $planId['id'] : null;
    }
}
