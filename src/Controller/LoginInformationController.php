<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\LoginInformationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/login/information', name: 'app_login_information', methods: ['GET', 'POST'])]
class LoginInformationController extends AbstractController
{
    #[Route( name: 'app_login_information', methods: ['GET', 'POST'])]
    public function create(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LoginInformationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_register', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('login_information/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
