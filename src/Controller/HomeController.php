<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(#[CurrentUser()] User $currentUser): Response
    {
        if ($currentUser) {
            $roles = $currentUser->getRoles();
            if (in_array('ROLE_USER', $roles) && !in_array('ROLE_APPROVED', $roles)) {
                return $this->redirectToRoute('app_complete_profile');
            }
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
