<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route(name: 'app_admin_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->redirectToRoute('app_admin_user_index');
    }

}
