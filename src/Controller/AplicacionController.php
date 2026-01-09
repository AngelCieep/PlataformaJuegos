<?php

namespace App\Controller;

use App\Entity\Aplicacion;
use App\Form\AplicacionType;
use App\Repository\AplicacionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/aplicacion')]
final class AplicacionController extends AbstractController
{
    #[Route(name: 'app_aplicacion_index', methods: ['GET'])]
    public function index(AplicacionRepository $aplicacionRepository): Response
    {
        return $this->render('aplicacion/index.html.twig', [
            'aplicacions' => $aplicacionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_aplicacion_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aplicacion = new Aplicacion();
        $form = $this->createForm(AplicacionType::class, $aplicacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aplicacion);
            $entityManager->flush();

            return $this->redirectToRoute('app_aplicacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aplicacion/new.html.twig', [
            'aplicacion' => $aplicacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/status', name: 'app_aplicacion_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Aplicacion $aplicacion, EntityManagerInterface $entityManager): Response
    {
        $status = $request->request->get('status');

        if (in_array($status, ['activo', 'inactivo'])) {
            // Convertir string a boolean (activo => true, inactivo => false)
            $aplicacion->setEstado($status === 'activo');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_aplicacion_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_aplicacion_show', methods: ['GET'])]
    public function show(Aplicacion $aplicacion): Response
    {
        return $this->render('aplicacion/show.html.twig', [
            'aplicacion' => $aplicacion,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_aplicacion_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Aplicacion $aplicacion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AplicacionType::class, $aplicacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_aplicacion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('aplicacion/edit.html.twig', [
            'aplicacion' => $aplicacion,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_aplicacion_delete', methods: ['POST'])]
    public function delete(Request $request, Aplicacion $aplicacion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aplicacion->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aplicacion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_aplicacion_index', [], Response::HTTP_SEE_OTHER);
    }
}

