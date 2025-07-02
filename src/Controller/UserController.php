<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Enum\TransactionType;
use App\Form\CompleteProfileType;
use App\Form\PlanRenewalType;
use App\Form\PlanPriceType;
use App\Form\SettingsType;
use App\Repository\PlanRepository;
use App\Repository\SubscriptionRepository;
use App\Repository\TransactionRepository;
use App\Service\PaymentManager;
use App\Service\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Invoice;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly ContainerBagInterface $params,
    ) {
        Stripe::setApiKey($this->params->get('stripe_api_private_key'));
    }

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
            $this->addFlash('success', 'Modifications enregistrées.');
            return $this->redirectToRoute('app_user_settings', [], Response::HTTP_SEE_OTHER);
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
        Request $request,
        TransactionRepository $transactionRepository,
        SubscriptionManager $subscriptionManager,
        PaymentManager $paymentManager,
    ): Response {
        $activePlan = $planRepository->findOneActivePlanByUser($currentUser);
        $activeTransaction = $transactionRepository->findOneActivePlanTransactionByUser($currentUser);
        $plans = $planRepository->findAllSorted();

        $plansWithForms = [];
        foreach ($plans as $plan) {
            $form = $this->createForm(PlanPriceType::class, null, [
                'price' => $plan->getPrice(),
                'attr' => ['id' => 'form_plan_' . $plan->getId()],
                'planId' => $plan->getId(),
            ]);
            $form->handleRequest($request);

            $plansWithForms[] = [
                'plan' => $plan,
                'formView' => $form->createView(),
                'form' => $form,
            ];
        }

        foreach ($plansWithForms as $planWithForm) {
            $form = $planWithForm['form'];
            if ($form->isSubmitted() && $form->isValid()) {
                $price = $form->get('price')->getData();
                $plan = $planRepository->findOneBy(["id" => $form->get('plan')->getData()]);
                $subscription = $subscriptionManager->createSubscription($plan, $price);
                return $this->redirectToRoute('app_payment', ["id" => $subscription->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        $renewalForm = null;
        if ($activeTransaction) {
            $renewalForm = $this->createForm(PlanRenewalType::class, null, [
                "renewal" => $activeTransaction->isRenewal(),
            ]);
            $renewalForm->handleRequest($request);
    
            if ($renewalForm->isSubmitted() && $renewalForm->isValid()) {
                $renewal = $renewalForm->get('renewal')->getData();
    
                try {
                    if ($renewal === "true") {
                        $paymentManager->enableRenewal($activeTransaction);
                        $this->addFlash('success', 'Renouvellement activé avec succès.');
                    } else {
                        $paymentManager->disableRenewal($activeTransaction);
                        $this->addFlash('danger', 'Renouvellement désactivé avec succès.');
                    }
                } catch (\Throwable) {
                    $this->addFlash('warning', 'Une erreur est survenue. Si le problème persiste, veuillez contacter le support.');
                }
    
                return $this->redirectToRoute('app_user_plan', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->render('user/plan.html.twig', [
            'currentUser' => $currentUser,
            'activePlan' => $activePlan,
            'plans' => $plans,
            'activeTransaction' => $activeTransaction,
            'renewalForm' => $renewalForm,
            'plansWithForms' => $plansWithForms,
        ]);
    }

    #[Route('/invoice', name: 'app_user_invoice', methods: ['GET'])]
    public function invoice(
        #[CurrentUser] User $currentUser,
        TransactionRepository $transactionRepository,
    ): Response {
        $invoices = [];

        $transactions = $transactionRepository->findBy(['user' => $currentUser]);

        foreach ($transactions as $transaction) {
            $subscription = $transaction->getSubscription();
            $donation = $transaction->getDonation();

            if ($subscription || $donation) {
                $invoices[] = [
                    'transaction' => $transaction,
                    'subscription' => $subscription,
                    'donation' => $donation,
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
    ): Response|RedirectResponse {
        
        if($transaction->getType() === TransactionType::Donation) {
            $filePath = $transaction->getInvoice()->getFilePath();
    
            $finder = new Finder();
            $finder->files()->in(dirname((string) $filePath))->name(basename((string) $filePath));
    
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
                    'Content-Disposition' => 'inline; filename="invoice_'.$transaction->getInvoice()->getInvoiceId().'.pdf"',
                ]
            );
        } else {
            $session = Session::retrieve($transaction->getSessionId());
            $invoice = Invoice::retrieve($session->invoice);
            $invoice_url = $invoice->hosted_invoice_url;
            return new RedirectResponse($invoice_url);
        }
    }
}
