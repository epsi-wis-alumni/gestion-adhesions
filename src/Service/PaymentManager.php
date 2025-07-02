<?php

namespace App\Service;

use App\Entity\Donation;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Refund;
use Stripe\Stripe;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class PaymentManager
{
    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private ContainerBagInterface $params,
        private EntityManagerInterface $entityManager,
    ) {
        Stripe::setApiKey($this->params->get('stripe_api_private_key'));
    }

    public function createTransaction(User $currentUser, Subscription|Donation $entity): Transaction
    {
        $amount = match ($entity::class) {
            Subscription::class => $entity->getPrice() && $entity->getPrice() > $entity->getPlan()->getPrice() ?
                $entity->getPrice() :
                $entity->getPlan()->getPrice(),
            Donation::class => $entity->getAmount(),
        };
        $type = match ($entity::class) {
            Subscription::class => TransactionType::Subscription,
            Donation::class => TransactionType::Donation,
        };
        $subscription = match ($entity::class) {
            Subscription::class => $entity,
            Donation::class => null,
        };
        $donation = match ($entity::class) {
            Subscription::class => null,
            Donation::class => $entity,
        };

        $transaction = new Transaction();
        $transaction
            ->setStatus(TransactionStatus::Create)
            ->setType($type)
            ->setAmount($amount)
            ->setUser($currentUser)
            ->setSubscription($subscription)
            ->setDonation($donation)
        ;

        return $transaction;
    }

    public function createSession(User $currentUser, Transaction $transaction, TransactionType $type): Session
    {
        if (TransactionType::Subscription == $type) {
            $mode = 'subscription';
            $lineItems = [
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
            ];
        } else {
            $mode = 'payment';
            $lineItems = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Donation',
                    ],
                    'unit_amount' => $transaction->getAmount() * 100,
                ],
            ];
        }

        return Session::create([
            'line_items' => [
                $lineItems,
            ],
            'mode' => $mode,
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

    public function disableRenewal(Transaction $transaction): void
    {
        $subId = Session::retrieve($transaction->getSessionId())->subscription;
        try {
            \Stripe\Subscription::update(
                $subId,
                [
                    'cancel_at_period_end' => true,
                ]
            );
            $transaction->setRenewal(false);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \Exception();
        }
    }

    public function enableRenewal(Transaction $transaction): void
    {
        $subId = Session::retrieve($transaction->getSessionId())->subscription;
        try {
            \Stripe\Subscription::update(
                $subId,
                [
                    'cancel_at_period_end' => false,
                ]
            );
            $transaction->setRenewal(true);
            $this->entityManager->flush();
        } catch (\Exception) {
            throw new \Exception();
        }
    }

    public function getPriceToRefund(Transaction $activeTransaction): float
    {
        $oldPrice = $activeTransaction->getAmount();
        $createdAt = $activeTransaction->getCreatedAt();
        $now = new \DateTimeImmutable();
        $year = (new \DateTimeImmutable())->format('Y');
        $isLeapYear = date('L', strtotime("$year-01-01"));
        $daysInYear = '' !== $isLeapYear && '0' !== $isLeapYear ? 366 : 365;
        $secondsInYear = $daysInYear * 24 * 60 * 60;

        $interval = $now->getTimestamp() - $createdAt->getTimestamp();

        $ratio = $interval / $secondsInYear;

        return round($oldPrice * (1 - $ratio), 2);
    }

    public function getChargeBySessionId(string $sessionId): \Stripe\Charge
    {
        $session = Session::retrieve($sessionId);
        $customerId = $session->customer;
        $charges = \Stripe\Charge::all([
            'customer' => $customerId,
            'limit' => 1,
            'status' => 'succeeded',
        ]);

        return $charges['data'][0];
    }

    public function createRefund(Transaction $activeTransaction, float $priceRender)
    {
        $chargeId = $this->getChargeBySessionId($activeTransaction->getSessionId())->id;

        return Refund::create([
            'charge' => $chargeId,
            'amount' => $priceRender * 100,
        ]);
    }
}
