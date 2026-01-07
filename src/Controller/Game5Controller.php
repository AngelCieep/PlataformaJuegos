<?php

namespace App\Controller;

use App\Repository\JuegoRepository;
use App\Repository\PartidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game5Controller extends AbstractController
{
    #[Route('/game5', name: 'app_game5')]
    public function index(JuegoRepository $juegoRepository, PartidaRepository $partidaRepository): Response
    {
        $user = $this->getUser();

        return $this->render('game5/index.html.twig', [
            'controller_name' => 'Game5Controller',
            'user' => $user,
            'api_key' => 'ABCDEFGHIJK1234567890',
            'game_token' => 'SPACE_GAME_TOKEN_005',
        ]);
    }
}
