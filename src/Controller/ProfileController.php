<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\EditProfileType;
use App\Form\SettingsType;
use App\Repository\PlanRepository;
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

    #[Route('/plan', name: 'app_plan_show', methods: ['GET'])]
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
}
