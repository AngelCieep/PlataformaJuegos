<?php

namespace App\Controller;

use App\Entity\Juego;
use App\Repository\JuegoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game1Controller extends AbstractController
{
    #[Route('/juego/{id}', name: 'app_juegos')]
    public function index(int $id, JuegoRepository $juegoRepository): Response
    {
        // Buscar el juego en la base de datos por ID
        $juego = $juegoRepository->find($id);
        
        // Si el juego no existe o no está activo, redirigir a la plataforma de juegos
        if (!$juego || !$juego->isEstado()) {
            $this->addFlash('error', 'El juego solicitado no está disponible.');
            return $this->redirectToRoute('app_game_platform');
        }
        
        $user = $this->getUser();

        return $this->render('juegos/juego'.$id.'.html.twig', [
            'user' => $user,
            'juego' => $juego,
            'api_key' => $juego->getAplicacion()->getApiKey(),
            'game_token' => $juego->getTokenJuego(),
        ]);
    }
}
