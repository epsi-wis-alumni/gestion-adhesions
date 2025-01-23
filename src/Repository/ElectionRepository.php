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
     * @return Election[] Returns an array of Election objects
     */
    public function findOpened(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.voteStartAt <= :now')
            ->andWhere('e.voteEndAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Election[] Returns an array of Election objects
     */
    public function findClosed(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.voteEndAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.id', 'ASC')
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
