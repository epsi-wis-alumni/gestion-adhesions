<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
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

    // public function isRejected(int $id): bool
    // {
    //     $result = $this->createQueryBuilder('u')
    //         ->andWhere('u.id = :id')
    //         ->andWhere('u.rejectAt IS NOT NULL')
    //         ->setParameter('id', $id)
    //         ->getQuery()
    //         ->getOneOrNullResult();

    //     return $result !== null;
    // }

    // public function isApproved(int $id): bool
    // {
    //     $result = $this->createQueryBuilder('u')
    //         ->andWhere('u.id = :id')
    //         ->andWhere('u.approvedAt IS NULL')
    //         ->setParameter('id', $id)
    //         ->getQuery()
    //         ->getOneOrNullResult();

    //     return $result !== null;
    // }


    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
