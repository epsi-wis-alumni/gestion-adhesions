<?php

namespace App\Service;

use App\Entity\Plan;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

final readonly class SubscriptionManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function createSubscription(Plan $plan, string $price): Subscription
    {
        $subscription = new Subscription();
        $subscription
            ->setPlan($plan)
            ->setPrice($price)
            ->setDiscount(0)
        ;

        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $subscription;
    }
}
