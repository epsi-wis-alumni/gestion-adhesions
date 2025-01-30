<?php

namespace App\Repository;

use App\Entity\Newsletter;
use App\Entity\UserNewsletter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserNewsletter>
 */
class UserNewsletterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserNewsletter::class);
    }

    /**
     * @return UserNewsletter[] Returns an array of User objects
     */
    public function findByNewsletter(
        Newsletter $newsletter,
    ): array {
        return $this->createQueryBuilder('un')
            ->where('un.newsletter = :newsletter')
            ->andWhere('un.sentAt IS NULL')
            ->setParameter('newsletter', $newsletter)
            ->getQuery()
            ->getResult();
    }
}
