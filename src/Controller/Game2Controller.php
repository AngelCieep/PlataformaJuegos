<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game2Controller extends AbstractController
{
    #[Route('/game2', name: 'app_game2')]
    public function index(): Response
    {
        return $this->render('game2/index.html.twig', [
            'controller_name' => 'Game2Controller',
        ]);
    }
}
