<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserUpdateType;
use App\Repository\UserRepository;
use App\Service\UserManager;
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
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $perPage = $request->get('perPage', 50);
        $page = $request->get('page', 1);
        $totalUserCount = count($userRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
        ));
        $users = $userRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
            search: $request->get('search'),
        );
        $userCount = count($users);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'pages' => ceil($userCount / $perPage),
            'page' => $page,
            'user_count' => $request->get('search') ? count($users) . "/" . $totalUserCount : $totalUserCount,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user): Response
    {
        $form = $this->createForm(AdminUserUpdateType::class, $user, [
            'attr' => ['id' => 'admin_user_edit_form']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, #[CurrentUser()] User $currentUser, EntityManagerInterface $entityManager, User $user, UserManager $userManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userManager->delete(who: $user, by: $currentUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/{id}/approve', name: 'app_admin_user_approve', methods: ['GET'])]
    public function approve(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, User $user, UserManager $userManager): Response
    {
        $userManager->approve(who: $user, by: $currentUser);
        $userManager->addRole(to: $user, role: $user::ROLE_APPROVED);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_admin_user_reject', methods: ['GET'])]
    public function reject(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, User $user, UserManager $userManager): Response
    {
        $userManager->reject(who: $user, by: $currentUser);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
