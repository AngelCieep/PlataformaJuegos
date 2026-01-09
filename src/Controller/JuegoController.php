<?php

namespace App\Controller;

use App\Entity\Juego;
use App\Form\JuegoType;
use App\Repository\JuegoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/juego')]
final class JuegoController extends AbstractController
{
    #[Route(name: 'app_juego_index', methods: ['GET'])]
    public function index(JuegoRepository $juegoRepository): Response
    {
        return $this->render('juego/index.html.twig', [
            'juegos' => $juegoRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_juego_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $juego = new Juego();
        $form = $this->createForm(JuegoType::class, $juego);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($juego);
            $entityManager->flush();

            return $this->redirectToRoute('app_juego_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('juego/new.html.twig', [
            'juego' => $juego,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/status', name: 'app_juego_update_status', methods: ['POST'])]
    public function updateStatus(Request $request, Juego $juego, EntityManagerInterface $entityManager): Response
    {
        $status = $request->request->get('status');

        if (in_array($status, ['activo', 'inactivo'])) {
            // Convertir string a boolean (activo => true, inactivo => false)
            $juego->setEstado($status === 'activo');
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_juego_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_juego_show', methods: ['GET'])]
    public function show(Juego $juego): Response
    {
        return $this->render('juego/show.html.twig', [
            'juego' => $juego,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_juego_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Juego $juego, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(JuegoType::class, $juego);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_juego_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('juego/edit.html.twig', [
            'juego' => $juego,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_juego_delete', methods: ['POST'])]
    public function delete(Request $request, Juego $juego, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$juego->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($juego);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_juego_index', [], Response::HTTP_SEE_OTHER);
    }
}

