<?php

namespace App\Repository;

use App\Entity\Newsletter;
use App\Entity\User;
use App\Repository\Trait\OrderableTrait;
use App\Repository\Trait\PaginableTrait;
use App\Repository\Trait\SearchableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    use PaginableTrait;
    use OrderableTrait;
    use SearchableTrait;

    public const SEARCH_FIELDS = ['firstname', 'lastname', 'email', 'company', 'jobTitle'];

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $user, bool $flush = false): void
    {
        $this->getEntityManager()->persist($user);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
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
    public function findBySearchPaginated(int $page = 1, int $perPage = 50, array $orderBy = [], ?string $search = null): array
    {
        $qb = $this->createQueryBuilder('u');

        $this->paginate($qb, $page, $perPage);
        $this->orderBy($qb, $orderBy);

        if ($search) {
            $this->search($qb, $search, self::SEARCH_FIELDS);
        }

        $qb->andWhere('u.deletedAt IS NULL');

        return $qb->getQuery()->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByAllowNewsletter(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.settings.allowNewsletters = :allowNewsletters')
            ->setParameter('allowNewsletters', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByAllowNotifications(): array
    {
        return $this->createQueryBuilder('u')
            ->where('u.settings.allowNotifications = :allowNotifications')
            ->setParameter('allowNotifications', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return User[] Returns an array of User objects
     */
    public function findByReceiveNewsletter(Newsletter $newsletter): array
    {
        return $this->createQueryBuilder('u')
            ->join('u.userNewsletters', 'un')
            ->where('un.newsletter = :newsletter')
            ->andWhere('un.sentAt IS NOT NULL')
            ->setParameter('newsletter', $newsletter)
            ->getQuery()
            ->getResult();
    }
}
