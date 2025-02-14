<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Subscription;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class TransactionManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function create(
        User $user,
        Subscription $subscription,
        int $status,
        int $type,
        string $amount,
        DateTimeImmutable $createdAt = New DateTimeImmutable()
    ): Transaction {
        $transaction = new Transaction();
        $transaction
            ->setUser($user)
            ->setSubscription($subscription)
            ->setStatus($status)
            ->setType($type)
            ->setAmount($amount)
            ->setCreatedAt($createdAt)
        ;
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
        return $transaction;
    }
}