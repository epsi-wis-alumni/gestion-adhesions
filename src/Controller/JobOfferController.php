<?php

namespace App\Controller;

use App\Entity\JobOffer;
use App\Entity\JobQuestion;
use App\Entity\User;
use App\Form\JobOfferSortingType;
use App\Form\JobOfferType;
use App\Form\JobQuestionType;
use App\Repository\CategoryRepository;
use App\Repository\JobOfferRepository;
use App\Repository\JobQuestionRepository;
use App\Service\JobQuestionManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/job-offer')]
final class JobOfferController extends AbstractController
{
    #[Route(name: 'app_job_offer_index', methods: ['GET', 'POST'])]
    public function index(
        JobOfferRepository $jobOfferRepository,
        Request $request,
        CategoryRepository $categoryRepository,  
    ): Response {
        $jobOffers = $jobOfferRepository->findAll();
        $categories = $categoryRepository->findAll();
        $form = $this->createForm(JobOfferSortingType::class, null, [
            'job_offer_categories' => $categories,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobOffers = $jobOfferRepository->findFilteredJobOffers($form->getData());
        }

        return $this->render('job_offer/index.html.twig', [
            'jobOffers' => $jobOffers,
            'form' => $form,
        ]);
    }

    #[Route('/new', name: 'app_job_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobOffer = new JobOffer();
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobOffer);
            $entityManager->flush();

            return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_offer/new.html.twig', [
            'jobOffer' => $jobOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_offer_show', methods: ['GET', 'POST'])]
    public function show(
        JobOffer $jobOffer,
        Request $request,
        #[CurrentUser] User $currentUser,
        JobQuestionManager $jobQuestionManager,
        EntityManagerInterface $entityManager,
        JobQuestionRepository $jobQuestionRepository,
    ): Response {
        $newJobQuestion = new JobQuestion();
        $form = $this->createForm(JobQuestionType::class, $newJobQuestion);
        $form->handleRequest($request);
        $jobQuestions = $jobQuestionRepository->findBy(['jobOffer' => $jobOffer]);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobQuestionManager->create(
                $newJobQuestion, 
                $form->get('description')->getData(), 
                $jobOffer, 
                $currentUser,
            );

            $entityManager->persist($newJobQuestion);
            $entityManager->flush();
            
            return $this->redirectToRoute('app_job_offer_show', ['id' => $jobOffer->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_offer/show.html.twig', [
            'jobOffer' => $jobOffer,
            'form' => $form,
            'jobQuestions' => $jobQuestions,
            'currentUser' => $currentUser,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_job_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JobOfferType::class, $jobOffer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_offer/edit.html.twig', [
            'jobOffer' => $jobOffer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_offer_delete', methods: ['POST'])]
    public function delete(Request $request, JobOffer $jobOffer, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobOffer->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($jobOffer);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_job_offer_index', [], Response::HTTP_SEE_OTHER);
    }
}

