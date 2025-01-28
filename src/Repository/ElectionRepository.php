<?php

namespace App\Repository;

use App\Entity\Election;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Election>
 */
class ElectionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Election::class);
    }

    /**
     * @return Election[]
     */
    public function findPending(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('e')
            ->andWhere('e.voteStartAt > :now')
            ->setParameter('now', $now)
            ->orderBy('e.voteStartAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Election[]
     */
    public function findInProgress(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('e')
            ->andWhere('e.voteStartAt <= :now')
            ->andWhere('e.voteEndAt >= :now')
            ->setParameter('now', $now)
            ->orderBy('e.voteStartAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Election[]
     */
    public function findDone(): array
    {
        $now = new \DateTimeImmutable();

        return $this->createQueryBuilder('e')
            ->andWhere('e.voteEndAt < :now')
            ->setParameter('now', $now)
            ->orderBy('e.voteEndAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return bool Returns an bool
     */
    public function isClosed($id): bool
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->andWhere('e.voteEndAt < :now')
            ->setParameter('id', $id)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->getOneOrNullResult() ? true : false;;
    }

    //    public function findOneBySomeField($value): ?Election
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
