<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * @return Event[] Returns an array of Event objects
     */
    public function findByPublic(bool $value = true): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.private != :private')
            ->setParameter('private', $value)
            ->getQuery()
            ->getResult()
        ;
    }

     /**
     * @return Event[]
     */
    public function findPending(bool $onlyPublic = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.startAt > :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'ASC')
        ;

        if ($onlyPublic) {
            $qb->andWhere('e.private = FALSE');
        }

        return $qb
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Event[]
     */
    public function findInProgress(bool $onlyPublic = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.startAt <= :now')
            ->andWhere('e.endAt >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.startAt', 'ASC')
        ;

        if ($onlyPublic) {
            $qb->andWhere('e.private = FALSE');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[]
     */
    public function findDone(bool $onlyPublic = false): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.endAt < :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.endAt', 'DESC')
        ;

        if ($onlyPublic) {
            $qb->andWhere('e.private = FALSE');
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Event[] latest events
     */
    public function findLastest($quantity): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults($quantity)
            ->getQuery()
            ->getResult()
        ;
    }
}
