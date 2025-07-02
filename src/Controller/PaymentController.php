<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use App\Enum\TransactionType;
use App\Repository\TransactionRepository;
use App\Service\InvoiceManager;
use App\Service\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PaymentController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ContainerBagInterface $params,
    ) {
        Stripe::setApiKey($this->params->get('stripe_api_private_key'));
    }

    #[Route('/order/subscription/{id}', name: 'app_payment', methods: ['GET'])]
    public function create(
        Subscription $subscription,
        PaymentManager $paymentManager,
        #[CurrentUser()] User $currentUser,
        TransactionRepository $transactionRepository,
    ): Response {
        $activeTransaction = $transactionRepository->findOneActivePlanTransactionByUser($currentUser);

        $transaction = $paymentManager->createTransaction($currentUser, $subscription);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        try {
            $session = $paymentManager->createSession($currentUser, $transaction, TransactionType::Subscription);

            $transaction->setSessionId($session->id);
            $transaction->setStatus(TransactionStatus::Pending);

            $this->entityManager->flush();
        } catch (\Exception) {
            $this->addFlash('error', 'Erreur lors de la création de la session Stripe.');

            return $this->redirectToRoute('app_payment_error', ['id' => $transaction->getId()]);
        }

        if ($activeTransaction instanceof Transaction) {
            $activeTransaction->setStatus(TransactionStatus::RefundPending);
            $this->entityManager->flush();
        }

        return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
    }

    #[Route('/order/success/{id}', name: 'app_payment_success')]
    public function stripeSuccess(
        Transaction $transaction,
        InvoiceManager $invoiceManager,
    ): Response {
        try {
            $session = Session::retrieve($transaction->getSessionId());

            return $this->render('order/success.html.twig', [
                'session' => $session,
            ]);
        } catch (\Exception $e) {
            return $this->render('order/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    #[Route('/order/error/{id}', name: 'app_payment_error')]
    public function stripeError(
        Transaction $transaction,
    ): Response {
        try {
            $session = Session::retrieve($transaction->getSessionId());
            $session->expire();
        } catch (\Exception $e) {
            return $this->render('order/error.html.twig', [
                'error' => $e->getMessage(),
                'transaction' => $transaction,
            ]);
        }

        return $this->render('order/error.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/order/webhook', name: 'app_payment_webhook', methods: ['POST'])]
    public function stripeWebhook(
        Request $request,
        InvoiceManager $invoiceManager,
        TransactionRepository $transactionRepository,
        EntityManagerInterface $entityManager,
        PaymentManager $paymentManager,
    ): Response {
        $payload = $request->getContent();
        $signature = $request->headers->get('Stripe-Signature');
        $endpointSecret = $this->params->get('stripe_webhook_secret');

        $sessionId = '';

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $endpointSecret
            );
        } catch (\UnexpectedValueException) {
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        if ('checkout.session.completed' === $event->type) {
            $sessionId = $event->data->object->id;
            $transaction = $transactionRepository->findOneBy(['sessionId' => $sessionId]);
            $activeTransactionRefundPending = $transactionRepository->findOneActiveTransactionRefundPendingByUser($transaction->getUser());

            if ('paid' === $event->data->object->payment_status) {
                $transaction->setStatus(TransactionStatus::Completed);

                if (TransactionType::Donation === $transaction->getType()) {
                    $invoice_id = $invoiceManager->generateInvoiceId();
                    $transaction->getInvoice()->setInvoiceId($invoice_id);

                    $html = $this->render('invoice/invoice.html.twig', [
                        'transaction' => $transaction,
                    ]);
                    $invoiceManager->create(
                        html: $html,
                        transaction: $transaction
                    );
                }
                if (TransactionType::Subscription === $transaction->getType()) {
                    $invoiceManager->sendInvoiceLink($transaction);
                }

                if ($activeTransactionRefundPending instanceof Transaction) {
                    $priceRender = $paymentManager->getPriceToRefund($activeTransactionRefundPending);
                    $refund = $paymentManager->createRefund($activeTransactionRefundPending, $priceRender);

                    $activeTransactionRefundPending->setStatus(TransactionStatus::RefundCompleted);
                    $activeTransactionRefundPending->setRefundAmount($priceRender);
                    $activeTransactionRefundPending->setRefundId($refund->id);
                }
            }
            if ('unpaid' === $event->data->object->payment_status) {
                $transaction->setStatus(TransactionStatus::Failed);
            }
        }

        $entityManager->flush();

        return new Response('Webhook handled', Response::HTTP_OK);
    }

    #[Route('/order/donation/{id}', name: 'app_payment_donation', methods: ['GET'])]
    public function stripeDonation(
        Donation $donation,
        #[CurrentUser()] User $currentUser,
        PaymentManager $paymentManager,
    ): Response {
        $transaction = $paymentManager->createTransaction($currentUser, $donation);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        try {
            $session = $paymentManager->createSession($currentUser, $transaction, TransactionType::Donation);

            $transaction->setSessionId($session->id);
            $transaction->setStatus(TransactionStatus::Pending);

            $this->entityManager->flush();
        } catch (\Exception) {
            $this->addFlash('error', 'Erreur lors de la création de la session Stripe.');

            return $this->redirectToRoute('app_payment_error', ['id' => $transaction->getId()]);
        }

        return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
    }
}
