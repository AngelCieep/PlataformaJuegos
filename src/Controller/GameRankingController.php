<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameRankingController extends AbstractController
{
    #[Route('/game/ranking', name: 'app_game_ranking')]
    public function index(): Response
    {
        return $this->render('game_ranking/index.html.twig', [
            'controller_name' => 'GameRankingController',
        ]);
    }
}
