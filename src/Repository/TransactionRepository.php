<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findOneActivePlanTransactionByUser(User $user): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->where('t.user = :user')
            ->andWhere('t.createdAt >= :date')
            ->andWhere('t.status = :status1')
            ->orWhere('t.status = :status2')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('status1', TransactionStatus::Completed)
            ->setParameter('status2', TransactionStatus::RefundPending)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneActiveTransactionRefundPendingByUser(User $user): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->where('t.user = :user')
            ->andWhere('t.createdAt >= :date')
            ->andWhere('t.status = :status')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('status', TransactionStatus::RefundPending)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
