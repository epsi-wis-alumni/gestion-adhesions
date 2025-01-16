<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findPending(): array
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.approvedAt IS NULL')
            ->andWhere('u.rejectAt IS NULL')
            ->orderBy('u.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
    * @return User[] Returns an array of User objects
    */
    public function findAllPaginated(int $page = 1, int $perPage = 50, array $orderBy = []): array
    {
        $qb = $this->createQueryBuilder('u');
        $qb
            ->setMaxResults($perPage)
            ->setFirstResult(($page - 1) * $perPage)
        ;

        foreach ($orderBy as $sort => $order) {
            $qb->orderBy($sort, $order);
        }

        return $qb->getQuery()->getResult();
    }
}
