<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
                return $this->redirectToRoute('app_admin');
            }
            return $this->redirectToRoute('app_home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/login/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $nombre = $request->request->get('nombre');
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');

            // Validaciones
            if (empty($email) || empty($nombre) || empty($password) || empty($confirmPassword)) {
                $error = 'Todos los campos son obligatorios';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'El formato del email no es válido';
            } elseif ($password !== $confirmPassword) {
                $error = 'Las contraseñas no coinciden';
            } elseif (strlen($password) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres';
            } else {
                // Verificar si el email ya existe
                $existingUser = $userRepository->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $error = 'El email ya está registrado en el sistema';
                } else {
                    // Crear el nuevo usuario
                    $user = new User();
                    $user->setEmail($email);
                    $user->setNombre($nombre);
                    $user->setRoles(['ROLE_USER']);
                    $user->setEstado(true);
                    $user->setFechaRegistro(new \DateTimeImmutable());
                    
                    // Hash de la contraseña
                    $hashedPassword = $passwordHasher->hashPassword($user, $password);
                    $user->setToken($hashedPassword);

                    try {
                        $entityManager->persist($user);
                        $entityManager->flush();

                        $this->addFlash('success', 'Registro exitoso. Ya puedes iniciar sesión');
                        return $this->redirectToRoute('app_login');
                    } catch (\Exception $e) {
                        $error = 'Error al crear la cuenta. Inténtalo de nuevo';
                    }
                }
            }
        }

        return $this->render('security/register.html.twig', [
            'error' => $error,
        ]);
    }
}
