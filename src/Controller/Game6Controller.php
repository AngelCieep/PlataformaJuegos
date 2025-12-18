<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game6Controller extends AbstractController
{
    #[Route('/game6', name: 'app_game6')]
    public function index(): Response
    {
        return $this->render('game6/index.html.twig', [
            'controller_name' => 'Game6Controller',
        ]);
    }
}
