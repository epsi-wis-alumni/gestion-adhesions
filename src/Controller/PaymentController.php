<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionStatus;
use App\Repository\TransactionRepository;
use App\Service\InvoiceManager;
use App\Service\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\Request;
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
    ) {
        Stripe::setApiKey($this->params->get('stripe_api_private_key'));
    }

    #[Route('/subscription/{id}', name: 'app_payment', methods: ['GET'])]
    public function create(
        Subscription $subscription,
        PaymentManager $paymentManager,
        #[CurrentUser()] User $currentUser,
        TransactionRepository $transactionRepository,
    ): Response {
        $activeTransaction = $transactionRepository->findOneActivePlanTransactionByUser($currentUser);
        
        $transaction = $paymentManager->createTransation($currentUser, $subscription);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        try {
            $session = $paymentManager->createSession($currentUser, $transaction);

            $transaction->setSessionId($session->id);
            $transaction->setStatus(TransactionStatus::Pending);

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session Stripe.');
            
            return $this->redirectToRoute('app_payment_error', ['id' => $transaction->getId()]);
        }

        if ($activeTransaction) {
            $activeTransaction->setStatus(TransactionStatus::PendingRefund);
            $this->entityManager->flush();
        }

        return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/success/{id}', name: 'app_payment_success')]
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

    #[Route('/error/{id}', name: 'app_payment_error')]
    public function stripeError(
        Transaction $transaction
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

    #[Route('/webhook', name: 'app_payment_webhook', methods: ['POST'])]
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

        $sessionId = "";

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        if ($event->type === 'checkout.session.completed') {
            $sessionId = $event->data->object->id;
            $transaction = $transactionRepository->findOneBy(['sessionId' => $sessionId]);
            $activeTransactionPendingRefund = $transactionRepository->findOneActiveTransactionPendingRefundByUser($transaction->getUser());

            if ($event->data->object->payment_status === "paid") {
                $transaction->setStatus(TransactionStatus::Completed);
                $entityManager->flush();

                $html = $this->render('invoice/invoice.html.twig', [
                    'transaction' => $transaction,
                ]);
                $invoiceManager->create(
                    html: $html,
                    transaction: $transaction
                );

                if ($activeTransactionPendingRefund) {
                    $priceRender = $paymentManager->getPriceToRefund($activeTransactionPendingRefund);
                    $refund = $paymentManager->createRefund($activeTransactionPendingRefund, $priceRender);

                    $activeTransactionPendingRefund->setStatus(TransactionStatus::Refunded);
                    $activeTransactionPendingRefund->setRefundAmount($priceRender);
                    $activeTransactionPendingRefund->setRefundId($refund->id);

                    $this->entityManager->flush();
                }
            }
            if ($event->data->object->payment_status === "unpaid") {
                $transaction->setStatus(TransactionStatus::Failed);
                $entityManager->flush();
            }
        }

        $entityManager->flush();

        return new Response('Webhook handled', Response::HTTP_OK);
    }
}
