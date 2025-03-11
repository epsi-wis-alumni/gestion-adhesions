<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\CompleteProfileType;
use App\Form\PlanRenewalType;
use App\Form\SettingsType;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TransactionRepository;
use App\Service\PaymentManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Session\Session;

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

    #[Route('/plan', name: 'app_user_plan', methods: ['GET', 'POST'])]
    public function show(
        #[CurrentUser] User $currentUser,
        PlanRepository $planRepository,
        TransactionRepository $transactionRepository,
        Request $request,
        PaymentManager $paymentManager,
        Session $session,
    ): Response {
        $activePlan = $planRepository->findOneActivePlanByUser($currentUser);
        $activeTransaction = $transactionRepository->findOneActivePlanTransactionByUser($currentUser);
        $plans = $planRepository->findAllSorted();

        $renewalUpdated = "";

        $renewalForm = $this->createForm(PlanRenewalType::class, null, [
            "renewal" => $activeTransaction->isRenewal(),
        ]);
        $renewalForm->handleRequest($request);

        if ($renewalForm->isSubmitted() && $renewalForm->isValid()) {
            $renewal = $renewalForm->get('renewal')->getData();
        
            try {
                if ($renewal === "true") {
                    $paymentManager->addRenewal($activeTransaction);
                    $session->getFlashBag()->add('renewal_status', 'add');
                } else {
                    $paymentManager->removeRenewal($activeTransaction);
                    $session->getFlashBag()->add('renewal_status', 'remove');
                }
            } catch (\Throwable $th) {
                $session->getFlashBag()->add('renewal_status', 'error');
            }
    
            return $this->redirectToRoute('app_user_plan', [], Response::HTTP_SEE_OTHER);
        }
    
        $flashMessages = $session->getFlashBag()->get('renewal_status', []);
        $renewalUpdated = $flashMessages[0] ?? '';
        
        return $this->render('user/plan.html.twig', [
            'currentUser' => $currentUser,
            'activePlan' => $activePlan,
            'plans' => $plans,
            'activeTransaction' => $activeTransaction,
            'renewalForm' => $renewalForm,
            'renewalUpdated' => $renewalUpdated,
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

        $finder = new Finder();
        $finder->files()->in(dirname($filePath))->name(basename($filePath));

        if (!$finder->hasResults()) {
            throw $this->createNotFoundException('La facture demandée est introuvable.');
        }

        foreach ($finder as $file) {
            $contents = $file->getContents();
        }

        return new Response(
            $contents,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="invoice_'.$transaction->getId().'.pdf"',
            ]
        );
    }
}
