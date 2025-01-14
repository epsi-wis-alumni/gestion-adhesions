<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserUpdateType;
use App\Repository\UserRepository;
use App\Service\UserManager;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{

    #[Route(name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/{id}/update', name: 'app_admin_user_update', methods: ['POST', 'GET'])]
    public function update(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(AdminUserUpdateType::class, $user, [
            'attr' => ['id' => 'admin_user_update_form']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/update.html.twig', [
            'user'=> $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();    
        }
        
        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/approve',name: 'app_admin_user_approve', methods: ['GET'])]
    public function approve(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, User $user, UserManager $userManager): Response
    {
        $userManager->approve(who: $user, by: $currentUser);
        $entityManager->flush();

        return $this->render('admin/membership/index.html.twig', [
            'controller_name' => 'AdminMembershipController',
        ]);
    }

    #[Route('/{id}/reject',name: 'app_admin_user_reject', methods: ['GET'])]
    public function reject(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, User $user, UserManager $userManager): Response
    {
            $userManager->reject(who: $user, by: $currentUser);
            $entityManager->flush();

            return $this->render('admin/membership/index.html.twig', [
                'controller_name' => 'AdminMembershipController',
            ]);
    }
}
