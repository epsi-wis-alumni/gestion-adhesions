<?php

namespace App\Controller;

use App\Entity\Plan;
use App\Form\AdminPlanType;
use App\Repository\PlanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/plan')]
final class AdminPlanController extends AbstractController
{
    #[Route(name: 'app_admin_plan_index', methods: ['GET'])]
    public function index(PlanRepository $planRepository): Response
    {
        return $this->render('admin/plan/index.html.twig', [
            'plans' => $planRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_plan_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        PlanRepository $planRepository,
    ): Response {
        $plan = new Plan();
        $form = $this->createForm(AdminPlanType::class, $plan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plan->isHighlighted()) {
                $planRepository->resetAllHighlighted();
            }

            $entityManager->persist($plan);
            $entityManager->flush();
            return $this->redirectToRoute('app_admin_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/plan/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_plan_show', methods: ['GET'])]
    public function show(Plan $plan): Response
    {
        return $this->render('admin/plan/show.html.twig', [
            'plan' => $plan,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_plan_edit', methods: ['GET', 'POST'])]
    public function edit(
        Plan $plan, 
        Request $request, 
        EntityManagerInterface $entityManager,
        PlanRepository $planRepository,
    ): Response {
        $originalFeatures = new ArrayCollection($plan->getFeatures()->toArray());

        $form = $this->createForm(AdminPlanType::class, $plan);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plan->isHighlighted()) {
                $planRepository->resetAllHighlighted();
            }

            foreach ($originalFeatures as $feature) {
                if (!$plan->getFeatures()->contains($feature)) {
                    $plan->removeFeature($feature);
                    $entityManager->remove($feature);
                }
            }

            $entityManager->persist($plan);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_plan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/plan/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_plan_delete', methods: ['POST'])]
    public function delete(Request $request, Plan $plan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plan->getId(), $request->getPayload()->getString('_token'))) {
            foreach ($plan->getFeatures() as $feature) {
                $feature->setPlan(null);
                $entityManager->remove($feature);
            }

            $entityManager->remove($plan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_plan_index', [], Response::HTTP_SEE_OTHER);
    }

}
