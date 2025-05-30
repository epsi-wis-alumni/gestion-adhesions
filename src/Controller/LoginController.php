<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\MemberType;
use App\Form\CompleteProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Form\FormError;
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

    #[Route('/complete-profile', name: 'app_complete_profile', methods: ['GET', 'POST'])]
    public function complete(
        Request $request, 
        #[CurrentUser()] User $currentUser, 
        EntityManagerInterface $entityManager,
        ContainerBagInterface $params,
    ): Response {
        $form = $this->createForm(CompleteProfileType::class, $currentUser, [
            'attr' => ['id' => 'login-information-form'],
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $type = $form->get('type')->getData();

            $studentCard = $form->get('studentCard')->getData();
            $degree = $form->get('degree')->getData();

            if ($type === MemberType::Student && !$studentCard) {
                $form->get('studentCard')->addError(new FormError('Veuillez fournir une carte étudiante.'));
            }

            if ($type === MemberType::Alumni && !$degree) {
                $form->get('degree')->addError(new FormError('Veuillez fournir un diplôme.'));
            }

            if ($form->isValid()) {
                $justificationFile = null;

                if ($type === MemberType::Student) {
                    $justificationFile = $studentCard;
                } elseif ($type === MemberType::Alumni) {
                    $justificationFile = $degree;
                }

                if ($justificationFile) {
                    $uploadsDir = $params->get('justification_file_path');
                    $filename = strtolower($type->name) . '_' . $currentUser->getLastname() . '_' . uniqid() . '.' . $justificationFile->guessExtension();

                    $justificationFile->move($uploadsDir, $filename);

                    $currentUser->setJustificationFilePath($uploadsDir . '/' . $filename);
                }

                $entityManager->flush();

                return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
            }
        }
        
        return $this->render('login/complete.html.twig', [
            'form' => $form,
        ]);
    }
}
