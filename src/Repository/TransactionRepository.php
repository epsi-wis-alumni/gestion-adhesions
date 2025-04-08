<?php

namespace App\Repository;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
use App\Repository\Trait\OrderableTrait;
use App\Repository\Trait\PaginableTrait;
use App\Repository\Trait\SearchableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    use PaginableTrait;
    use OrderableTrait;
    use SearchableTrait;
    
    public const SEARCH_FIELDS = [
        'amount',
        'refundAmount',
        'sessionId',
        'refundId',
        'u.firstname', // Champs de l'entité User
        'u.lastname',  // Champs de l'entité User
    ];

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
            ->andWhere('t.status = :completed')
            ->orWhere('t.status = :refund_pending')
            ->andWhere('t.type = :type')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('completed', TransactionStatus::Completed)
            ->setParameter('refund_pending', TransactionStatus::RefundPending)
            ->setParameter('type', TransactionType::Subscription)
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
            ->andWhere('t.type = :type')
            ->orderBy('t.createdAt', 'DESC')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTimeImmutable('-1 year'))
            ->setParameter('status', TransactionStatus::RefundPending)
            ->setParameter('type', TransactionType::Subscription)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findBySearchPaginated(int $page = 1, int $perPage = 50, string $sort = "", string $order = "", ?string $search = null): array
    {
        if (!in_array($sort, ['id', 'createdAt', 'status', 'type', 'amount', 'u.id', 'u.firstname', 'u.lastname', 'renewal', 'refundAmount'])) {
            $sort = 'createdAt';
        }

        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'desc';
        }

        $qb = $this
            ->createQueryBuilder('t')
            ->leftJoin('t.user', 'u')
        ;
    
        $this->paginate($qb, $page, $perPage);
    
        if ($search) {
            $this->search($qb, $search, self::SEARCH_FIELDS);
        }

        if (str_starts_with($sort, 'u.')) {
            $qb->addOrderBy($sort, $order);
        } else {
            $qb->addOrderBy('t.' . $sort, $order);
        }
    
        return $qb->getQuery()->getResult();
    }

    public function countBySearch(string $search): int
    {
        $qb = $this->createQueryBuilder('t');
        $qb
            ->select('COUNT(t.id)')
            ->leftJoin('t.user', 'u')
        ;
        $this->search($qb, $search, self::SEARCH_FIELDS);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
