<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\PlanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/plan')]
final class PlanController extends AbstractController
{
    #[Route(name: 'app_plan', methods: ['GET'])]
    public function plan(
        #[CurrentUser()] User $currentUser,
        PlanRepository $planRepository,
    ): Response {
        $activePlan = $planRepository->findOneActivePlanByUser($currentUser);
        $plans = $planRepository->findAllSorted();

        return $this->render('plan/plan.html.twig', [
            'currentUser' => $currentUser,
            'activePlan' => $activePlan,
            'plans' => $plans,
        ]);
    }
}
