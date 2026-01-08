<?php

namespace App\Controller;

use App\Entity\Partida;
use App\Form\PartidaType;
use App\Repository\PartidaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/partida')]
final class PartidaController extends AbstractController
{
    #[Route(name: 'app_partida_index', methods: ['GET'])]
    public function index(PartidaRepository $partidaRepository): Response
    {
        return $this->render('partida/index.html.twig', [
            'partidas' => $partidaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_partida_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $partida = new Partida();
        $form = $this->createForm(PartidaType::class, $partida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($partida);
            $entityManager->flush();

            return $this->redirectToRoute('app_partida_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partida/new.html.twig', [
            'partida' => $partida,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partida_show', methods: ['GET'])]
    public function show(Partida $partida): Response
    {
        return $this->render('partida/show.html.twig', [
            'partida' => $partida,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_partida_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Partida $partida, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PartidaType::class, $partida);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_partida_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('partida/edit.html.twig', [
            'partida' => $partida,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_partida_delete', methods: ['POST'])]
    public function delete(Request $request, Partida $partida, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$partida->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($partida);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_partida_index', [], Response::HTTP_SEE_OTHER);
    }
}
