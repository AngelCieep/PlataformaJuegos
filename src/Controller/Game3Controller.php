<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game3Controller extends AbstractController
{
    #[Route('/game3', name: 'app_game3')]
    public function index(): Response
    {
        return $this->render('game3/index.html.twig', [
            'controller_name' => 'Game3Controller',
        ]);
    }
}
