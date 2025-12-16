<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game4Controller extends AbstractController
{
    #[Route('/game4', name: 'app_game4')]
    public function index(): Response
    {
        return $this->render('game4/index.html.twig', [
            'controller_name' => 'Game4Controller',
        ]);
    }
}
