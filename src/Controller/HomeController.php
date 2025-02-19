<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ElectionRepository;
use App\Repository\PlanRepository;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Service\Manager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        #[CurrentUser()] User $currentUser,
        PlanRepository $planRepository,
        ElectionRepository $electionRepository,
        EventRepository $eventRepository,
        UserRepository $userRepository,
        Manager $manager,
    ): Response {
        if (!$currentUser->hasCompleteInfo()) {
            return $this->redirectToRoute('app_complete_profile', [], Response::HTTP_SEE_OTHER);
        }

        $userCount = $userRepository->count();
        
        $events = $manager->orderByStep($eventRepository->findLastest(3));
        
        $elections = $manager->orderByStep($electionRepository->findLastest(3));
        
        $activePlan = $planRepository->findOneActivePlanByUser($currentUser);
        $plans = $planRepository->findAllSorted();

        return $this->render('home/index.html.twig', [
            'date' => new \DateTimeImmutable(),
            'currentUser' => $currentUser,
            'userCount' => $userCount,
            'events' => $events,
            'elections' => $elections,
            'activePlan' => $activePlan,
            'plans' => $plans,
        ]);
    }
}
