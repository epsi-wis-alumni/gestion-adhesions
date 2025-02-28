<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/settings')]
final class AdminSettingsController extends AbstractController
{
    #[Route('/', name: 'app_admin_settings')]
    public function index(): Response
    {
        return $this->render('admin/settings/index.html.twig', [
            'controller_name' => 'AdminSettingsController',
        ]);
    }

    #[Route('/empty-cache', name: 'app_admin_settings_empty_cache')]
    public function emptyCache(): Response
    {
        opcache_reset();
        
        $this->addFlash('success', 'Le cache a bien été vidé.');

        return $this->redirectToRoute('app_admin_settings');
    }
}
