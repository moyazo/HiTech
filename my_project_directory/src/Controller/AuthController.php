<?php

namespace App\Controller;

use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\SignUpType; // Asegúrate de que el namespace sea correcto
use App\Entity\User;
use App\Repository\UserRepository;

class AuthController extends AbstractController
{

    #[Route('/auth', name: 'app_auth', methods: ['GET'])]
    public function index(): Response
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user, [
            'method' => 'POST',
            'action' => $this->generateUrl('signup'),
        ]);

        return $this->render('auth/index.html.twig', [
            'controller_name' => 'authController',
            'form'=> $form,
        ]);
    }

    #[Route('/auth/signup', name: 'signup', methods: ['POST'])]
    public function signup(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(SignUpType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           
            $formData = $form->getData();
            $hashedPass = $passwordHasher->hashPassword($user,$formData->getPassword());
            $user->setPassword($hashedPass);
            $user->setEmail($formData->getEmail());
            $user->setName($formData->getName());
            $user->setRole($formData->getRole());
            $salt = bin2hex(random_bytes(32)); // Generar un salt aleatorio
            $user->setSalt($salt);
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setUpdatedAt(new DateTimeImmutable());
            $completed = $userRepository->signup($user);
            if ($completed) {
                return $this->render('base.html.twig');
            } else {
                return $this->render('auth/index.html.twig', [
                    'controller_name' => 'authController',
                    'form' => $form,
                    'signError' => 'El usuario con este correo ya existe.',
                ]);
            }
        

           
        }

        return $this->render('auth/index.html.twig', [
            'form' => $form,
        ]);
    }
    
}
