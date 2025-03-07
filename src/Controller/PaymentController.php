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
use Psr\Log\LoggerInterface;
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
    ): Response {
        $transaction = $paymentManager->createTransation($currentUser, $subscription);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();

        try {
            $session = $paymentManager->createSession($currentUser, $transaction);

            // dd($session);
            $transaction->setSessionId($session->id);
            $transaction->setStripeSubscriptionId($session->subscription);
            $transaction->setStatus(TransactionStatus::Pending);

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session Stripe.');
            
            return $this->redirectToRoute('app_payment_error', ['id' => $transaction->getId()]);
        }

        return $this->redirect($session->url, Response::HTTP_SEE_OTHER);
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
