<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Service\InvoiceManager;
use App\Service\TransactionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser()] User $currentUser): Response
    {
        if (!$currentUser->hasCompleteInfo()) {
            return $this->redirectToRoute('app_complete_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/test', name: 'app_home_test')]
    public function test(
        TransactionManager $transactionManager,
        InvoiceManager $invoiceManager,
        #[CurrentUser()] User $user,
        SubscriptionRepository $subscriptionRepository,
    ): Response {
        $subscription = $subscriptionRepository->findOneBy(["id" => 1]);
        $transaction = $transactionManager->create(
            user: $user,
            subscription: $subscription,
            status: 1,
            type: 2,
            amount: 5.00
        );

        $html = $this->render('invoice/invoice.html.twig', [
            'transaction' => $transaction,
            'user' => $user,
        ]);
        $invoiceManager->create(
            html: $html,
            transaction: $transaction
        );
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
