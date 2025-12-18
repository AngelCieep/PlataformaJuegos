<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game5Controller extends AbstractController
{
    #[Route('/game5', name: 'app_game5')]
    public function index(): Response
    {
        return $this->render('game5/index.html.twig', [
            'controller_name' => 'Game5Controller',
        ]);
    }
}
