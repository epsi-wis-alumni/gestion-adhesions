<?php

namespace App\Service;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaymentManager
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function createTransation(User $currentUser, Subscription $subscription): Transaction
    {
        $transaction = new Transaction();
        $transaction->setStatus(TransactionStatus::Create);
        $transaction->setType(0);
        $transaction->setAmount($subscription->getPlan()->getPrice());
        $transaction->setCreatedAt();
        $transaction->setUser($currentUser);
        $transaction->setSubscription($subscription);

        return $transaction;
    }

    public function createSession(User $currentUser, Transaction $transaction): Session
    {
        return Session::create([
            'line_items' => [
                [
                    'quantity' => 1,
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $transaction->getSubscription()->getPlan()->getName(),
                        ],
                        'recurring' => [
                            'interval' => 'year',
                            'interval_count' => 1,
                        ],
                        'unit_amount' => $transaction->getAmount() * 100, // en centimes !
                    ],
                ],
            ],
            'mode' => 'subscription',
            'client_reference_id' => $currentUser->getId(),
            'success_url' => $this->urlGenerator->generate(
                'app_payment_success',
                ['id' => $transaction->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->urlGenerator->generate(
                'app_payment_error',
                ['id' => $transaction->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'customer_email' => $currentUser->getEmail(),
        ]);
    }
}
