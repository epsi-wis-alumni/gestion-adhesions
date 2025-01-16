<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginInformationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(): Response
    {
        return $this->render('login/index.html.twig');
    }

    #[Route('/complete-profile',name: 'app_complete_profile', methods: ['GET', 'POST'])]
    public function complete(Request $request, #[CurrentUser()] User $currentUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoginInformationFormType::class, $currentUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('login/complete.html.twig', [
            'user' => $currentUser,
            'form' => $form,
        ]);
    }
}
