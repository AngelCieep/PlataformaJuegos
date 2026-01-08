<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/role', name: 'app_user_update_role', methods: ['POST'])]
    public function updateRole(Request $request, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $role = $data['role'] ?? null;

            if (!$role || !in_array($role, ['ROLE_USER', 'ROLE_ADMIN'])) {
                return new JsonResponse(['success' => false, 'message' => 'Rol inválido'], 400);
            }

            $user->setRoles([$role]);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Rol actualizado correctamente']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Error al actualizar el rol'], 500);
        }
    }

    #[Route('/{id}/status', name: 'app_user_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, User $user, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $status = $data['status'] ?? null;

            if (!in_array($status, ['activo', 'inactivo'])) {
                return new JsonResponse(['success' => false, 'message' => 'Estado inválido'], 400);
            }

            $user->setEstado($status);
            $entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (\Exception $e) {
            return new JsonResponse(['success' => false, 'message' => 'Error al actualizar el estado'], 500);
        }
    }
}

