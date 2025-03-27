<?php

namespace App\Repository;

use App\Entity\JobOffer;
use App\Repository\Trait\SearchableTrait;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @extends ServiceEntityRepository<JobOffer>
 */
class JobOfferRepository extends ServiceEntityRepository
{
    use SearchableTrait;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobOffer::class);
    }

    /**
     * @param array $filters
     * @return JobOffer[] Returns an array of JobOffer objects
     */
    public function findFilteredJobOffers(array $filters): array
    {
        $qb = $this->createQueryBuilder('j');
        if (!empty($filters['types'])) {
            $qb->andWhere('j.type IN (:types)')
                ->setParameter('types', $filters['types']);
        }

        if (!empty($filters['categories'])) {
            $qb->join('j.categories', 'c')
                ->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $filters['categories']);
        }

        if (!empty($filters['experience'])) {
            $orX = $qb->expr()->orX();

            foreach ($filters['experience'] as $experienceLabel) {
                $experience = json_decode($experienceLabel, true);
                $min = $experience[0];
                $max = $experience[1];

                $andX = $qb->expr()->andX();

                if ($min !== null) {
                    $andX->add($qb->expr()->gte('j.requiredExperience', ':min_' . $min . '_' . $max));
                    $qb->setParameter('min_' . $min . '_' . $max, $min);
                }
                if ($max !== null) {
                    $andX->add($qb->expr()->lte('j.requiredExperience', ':max_' . $min . '_' . $max));
                    $qb->setParameter('max_' . $min . '_' . $max, $max);
                }

                $orX->add($andX);
            }

            $qb->andWhere($orX);
        }

        if (!$filters['languages']->isEmpty()) {
            $qb->join('j.languages', 'l')
                ->andWhere('l.id IN (:languages)')
                ->setParameter('languages', $filters['languages']);
        }

        if (!$filters['skills']->isEmpty()) {
            $qb->join('j.skills', 's')
                ->andWhere('s.id IN (:skills)')
                ->setParameter('skills', $filters['skills']);
        }

        if (!empty($filters['location'])) {
            $this->search($qb, $filters['location'], ['city', 'zipCode', 'country']);
        }

        $qb->orderBy('j.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }
}
