<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game1Controller extends AbstractController
{
    #[Route('/game1', name: 'app_game1')]
    public function index(): Response
    {
        return $this->render('game1/index.html.twig', [
            'controller_name' => 'Game1Controller',
            'api_key' => 'ABCDEFGHIJK1234567890',
            'game_token' => 'SNAKE_GAME_TOKEN_001',
        ]);
    }
}
