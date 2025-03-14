<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
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
            ->andWhere('t.type = :type')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('status1', TransactionStatus::Completed)
            ->setParameter('status2', TransactionStatus::PendingRefund)
            ->setParameter('type', TransactionType::Subscription)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOneActiveTransactionPendingRefundByUser(User $user): ?Transaction
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
            ->where('t.user = :user')
            ->andWhere('t.createdAt >= :date')
            ->andWhere('t.status = :status')
            ->andWhere('t.type = :type')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('status', TransactionStatus::PendingRefund)
            ->setParameter('type', TransactionType::Subscription)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
