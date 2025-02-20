<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CompleteProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Transaction;
use App\Form\SettingsType;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TransactionRepository;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/profile', name: 'app_user_profile', methods: ['POST', 'GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
    ): Response {
        $form = $this->createForm(CompleteProfileType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/profile.html.twig', [
            'currentUser' => $currentUser,
            'form' => $form,
        ]);
    }
        
    #[Route('/delete/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        User $user,
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/settings', name: 'app_user_settings', methods: ['POST', 'GET'])]
    public function settings(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
    ): Response {
        $form = $this->createForm(SettingsType::class, $currentUser->getSettings());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($currentUser);
            $entityManager->flush();
        }

        return $this->render('user/settings.html.twig', [
            'currentUser' => $currentUser,
            'form' => $form,
        ]);
    }

    #[Route('/plan', name: 'app_user_plan', methods: ['GET'])]
    public function show(
        #[CurrentUser] User $currentUser,
        PlanRepository $planRepository,
        SubscriptionRepository $subscriptionRepository,
    ): Response {
        $activePlan = $planRepository->findOneActivePlanByUser($currentUser);
        $plans = $planRepository->findAllSorted();

        return $this->render('user/plan.html.twig', [
            'currentUser' => $currentUser,
            'activePlan' => $activePlan,
            'plans' => $plans,
        ]);
    }

    #[Route('/invoice', name: 'app_user_invoice', methods: ['GET'])]
    public function invoice(
        #[CurrentUser] User $currentUser,
        TransactionRepository $transactionRepository,
        SubscriptionRepository $subscriptionRepository,
    ): Response {
        $invoices = [];

        $transactions = $transactionRepository->findBy(['user' => $currentUser]);

        foreach ($transactions as $transaction) {
            $subscription = $subscriptionRepository->findOneBy(['id' => $transaction->getSubscription()->getId()]);
            
            if ($subscription) {
                $invoices[] = [
                    'transaction' => $transaction,
                    'subscription' => $subscription,
                ];
            }
        }

        return $this->render('user/invoice.html.twig', [
            'invoices' => $invoices,
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/invoice/{id}', name: 'app_user_invoice_show', methods: ['GET'])]
    public function showInvoice(
        Transaction $transaction,
    ): Response {

        $filePath = $transaction->getInvoice()->getFilePath();

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('La facture demandée est introuvable.');
        }

        $pdfContent = file_get_contents($filePath);

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice_' . $transaction->getId() . '.pdf"',
            ]
        );
    }
}