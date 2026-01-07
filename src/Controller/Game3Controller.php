<?php

namespace App\Controller;

use App\Repository\JuegoRepository;
use App\Repository\PartidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game3Controller extends AbstractController
{
    #[Route('/game3', name: 'app_game3')]
    public function index(JuegoRepository $juegoRepository, PartidaRepository $partidaRepository): Response
    {
        $user = $this->getUser();

        return $this->render('game3/index.html.twig', [
            'controller_name' => 'Game3Controller',
            'user' => $user,
            'api_key' => 'ABCDEFGHIJK1234567890',
            'game_token' => 'MEMORY_GAME_TOKEN_003',
        ]);
    }
}
