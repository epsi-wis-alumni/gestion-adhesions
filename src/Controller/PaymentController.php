<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\InvoiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


#[Route('/order')]
class PaymentController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
        private ContainerBagInterface $params,
    ) {}

    #[Route('/create-session-stripe/{id}', name: 'app_payment', methods: ['POST'])]
    public function createStripeSession(
        Subscription $subscription,
        #[CurrentUser()] User $currentUser
    ): RedirectResponse {
        if (!$subscription) {
            return $this->redirectToRoute('app_user_plan'); 
        }

        $transaction = new Transaction();
        $transaction
            ->setSubscription($subscription)
            ->setAmount(
                $subscription->getPlan()->getPrice() - 
                $subscription->getPlan()->getPrice() * 
                $subscription->getDiscount()
            )
            ->setStatus(0);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        $planStripe = [
            [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $transaction->getAmount() * 100, // Convert to centimes - Need Integer
                    'product_data' => [
                        'name' => $subscription->getPlan()->getName(),
                    ],
                ],
                'quantity' => 1,
            ],
        ];

        $apiKey = $this->params->get('stripe_api_key');

        Stripe::setApiKey($apiKey); 

        $checkoutSession = Session::create([
            'customer_email' => $currentUser->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $planStripe,
            ],
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate(
                'order/success',
                ['id' => $transaction->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
            'cancel_url' => $this->urlGenerator->generate(
                'order/error',
                ['id' => $transaction->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);

        return new RedirectResponse($checkoutSession->url);
    }

    #[Route('/success/{id}', name: 'app_payment_success')]
    public function stripeSuccess(
        Transaction $transaction,
        #[CurrentUser()] User $currentUser,
        InvoiceManager $invoiceManager,
    ): Response {
        $transaction->setStatus(1);
        $this->entityManager->flush();

        $html = $this->render('invoice/invoice.html.twig', [
            'transaction' => $transaction,
            'user' => $currentUser,
        ]);
        $invoiceManager->create(
            html: $html,
            transaction: $transaction
        );

        return $this->render('order/success.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/error/{id}', name: 'app_payment_error')]
    public function stripeError(Transaction $transaction): Response
    {
        $transaction->setStatus(2);
        $this->entityManager->flush();

        return $this->render('order/error.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}
