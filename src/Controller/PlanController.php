<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Entity\User;
use App\Form\PlanPriceType;
use App\Form\PlanRenewalType;
use App\Repository\PlanRepository;
use App\Repository\TransactionRepository;
use App\Service\PaymentManager;
use App\Service\SubscriptionManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

final class PlanController extends AbstractController
{
    #[Route('/plan', name: 'app_plan', methods: ['GET'])]
    public function plan(
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
                'attr' => ['id' => 'form_plan_'.$plan->getId()],
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
                $plan = $planRepository->findOneBy(['id' => $form->get('plan')->getData()]);
                $subscription = $subscriptionManager->createSubscription($plan, $price);

                return $this->redirectToRoute('app_payment', ['id' => $subscription->getId()], Response::HTTP_SEE_OTHER);
            }
        }

        $renewalForm = null;
        if ($activeTransaction instanceof Transaction) {
            $renewalForm = $this->createForm(PlanRenewalType::class, null, [
                'renewal' => $activeTransaction->isRenewal(),
            ]);
            $renewalForm->handleRequest($request);

            if ($renewalForm->isSubmitted() && $renewalForm->isValid()) {
                $renewal = $renewalForm->get('renewal')->getData();

                try {
                    if ('true' === $renewal) {
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

        return $this->render('plan/plan.html.twig', [
            'currentUser' => $currentUser,
            'activePlan' => $activePlan,
            'plans' => $plans,
            'activeTransaction' => $activeTransaction,
            'renewalForm' => $renewalForm,
            'plansWithForms' => $plansWithForms,
        ]);
    }
}
