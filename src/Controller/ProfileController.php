<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\SettingsType;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TransactionRepository;
use App\Service\InvoiceManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route(name: 'app_profile_index', methods: ['POST', 'GET'])]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
    ): Response {
        $form = $this->createForm(EditProfileType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('profile/index.html.twig', [
            'currentUser' => $currentUser,
            'form' => $form,
        ]);
    }

    #[Route('/settings', name: 'app_profile_settings', methods: ['POST', 'GET'])]
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

        return $this->render('profile/settings.html.twig', [
            'currentUser' => $currentUser,
            'form' => $form,
        ]);
    }

    #[Route('/plan', name: 'app_profilee_plan', methods: ['GET'])]
    public function show(
        #[CurrentUser] User $currentUser,
        PlanRepository $planRepository,
    ): Response {
        $plan = $planRepository->findOneActivePlanByUser($currentUser);

        return $this->render('profile/plan.html.twig', [
            'currentUser' => $currentUser,
            'plan' => $plan,
        ]);
    }

    #[Route('/invoice', name: 'app_profile_invoice', methods: ['GET'])]
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

        return $this->render('profile/invoice.html.twig', [
            'invoices' => $invoices,
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/invoice/{id}', name: 'app_profile_show_invoice', methods: ['GET'])]
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
