<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin', methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $error = null;
        $success = null;

        if ($request->isMethod('POST')) {
            $action = $request->request->get('action');
            $token = new CsrfToken('admin_users', $request->request->get('_token'));
            if (!$csrfTokenManager->isTokenValid($token)) {
                throw new AccessDeniedException('CSRF token inválido');
            }

            if ($action === 'create') {
                $email = trim((string) $request->request->get('email'));
                $nombre = trim((string) $request->request->get('nombre'));
                $password = (string) $request->request->get('password');
                $isAdmin = $request->request->get('is_admin') === '1';

                if (!$email || !$nombre || !$password) {
                    $error = 'Todos los campos son obligatorios.';
                } elseif ($userRepository->findOneBy(['email' => $email])) {
                    $error = 'Ya existe un usuario con ese email.';
                } else {
                    $user = new User();
                    $user->setEmail($email);
                    $user->setNombre($nombre);
                    $user->setRoles($isAdmin ? ['ROLE_ADMIN'] : ['ROLE_USER']);
                    $hashed = $passwordHasher->hashPassword($user, $password);
                    $user->setToken($hashed);
                    $user->setEstado(true);
                    $user->setFechaRegistro(new \DateTimeImmutable());
                    // opcional: mantener campo rol legacy
                    $user->setRol($isAdmin ? 'ADMIN' : 'USER');

                    $em->persist($user);
                    $em->flush();
                    $success = 'Usuario creado correctamente.';
                }
            }

            if ($action === 'delete') {
                $userId = (int) $request->request->get('user_id');
                if ($userId === $this->getUser()?->getId()) {
                    $error = 'No puedes eliminar tu propio usuario mientras estás conectado.';
                } else {
                    $user = $userRepository->find($userId);
                    if ($user) {
                        $em->remove($user);
                        $em->flush();
                        $success = 'Usuario eliminado.';
                    }
                }
            }
        }

        $users = $userRepository->findAll();

        return $this->render('admin/index.html.twig', [
            'users' => $users,
            'error' => $error,
            'success' => $success,
        ]);
    }
}
