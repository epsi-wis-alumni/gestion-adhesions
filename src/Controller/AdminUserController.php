<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use App\Service\NotificationManager;
use App\Service\UserManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class AdminUserController extends AbstractController
{
    #[Route(name: 'app_admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $perPage = $request->get('perPage', 50);
        $page = $request->get('page', 1);

        $users = $userRepository->findBySearchPaginated(
            page: $page,
            perPage: $perPage,
            search: $request->get('search'),
        );

        $usersTotalCount = $userRepository->count();
        $usersCurrentPageCount = count($users);
        $usersMatchingSearchCount = $request->get('search')
            ? $userRepository->countBySearch($request->get('search'))
            : $usersTotalCount
        ;

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
            'pages' => ceil($usersMatchingSearchCount / $perPage),
            'page' => $page,
            'user_count' => $request->get('search') ? $usersCurrentPageCount.'/'.$usersMatchingSearchCount : $usersMatchingSearchCount,
        ]);
    }

    #[Route('/admin/user/{id}/edit', name: 'app_admin_user_edit', methods: ['POST', 'GET'])]
    public function edit(Request $request, EntityManagerInterface $entityManager, User $user, UserManager $userManager): Response
    {
        $form = $this->createForm(AdminUserType::class, $user, [
            'attr' => ['id' => 'admin_user_edit_form'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('isAdmin')->getData() ? $userManager->addRole($user, User::ROLE_ADMIN) : $userManager->removeRole($user, User::ROLE_ADMIN);

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/user/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, #[CurrentUser()] User $currentUser, EntityManagerInterface $entityManager, User $user, UserManager $userManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userManager->delete(who: $user, by: $currentUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_user_index');
    }

    #[Route('/admin/user/{id}/approve', name: 'app_admin_user_approve', methods: ['GET'])]
    public function approve(
        EntityManagerInterface $entityManager,
        #[CurrentUser()] User $currentUser,
        User $user,
        UserManager $userManager,
        NotificationManager $notificationManager,
    ): Response {
        $userManager->approve(who: $user, by: $currentUser);
        $userManager->addRole(to: $user, role: $user::ROLE_APPROVED);
        $entityManager->flush();

        $notificationManager->sendNotification(entity: $user, users: [$user]);

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/user/{id}/reject', name: 'app_admin_user_reject', methods: ['GET'])]
    public function reject(EntityManagerInterface $entityManager, #[CurrentUser()] User $currentUser, User $user, UserManager $userManager): Response
    {
        $userManager->reject(who: $user, by: $currentUser);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/admin/user/{id}/justification-File', name: 'app_admin_user_justification_File', methods: ['GET'])]
    public function showInvoice(
        User $user,
    ): Response|RedirectResponse {
        $filePath = $user->getJustificationFilePath();

        $finder = new Finder();
        $finder->files()->in(dirname((string) $filePath))->name(basename((string) $filePath));

        if (!$finder->hasResults()) {
            throw $this->createNotFoundException('Le document demandée est introuvable.');
        }

        foreach ($finder as $file) {
            $contents = $file->getContents();
        }

        return new Response(
            $contents,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename='.pathinfo((string) $filePath)['filename'].'.pdf',
            ]
        );
    }
}
