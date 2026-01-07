<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Game4Controller extends AbstractController
{
    #[Route('/game4', name: 'app_game4')]
    public function index(Request $request): Response
    {
        // Clave API para autenticación (reemplazar con la clave real)
        $apiKey = 'TU_CLAVE_API';
        
        // Token para identificar el juego (específico para el Juego 4)
        $tokenJuego = 'PONG_GAME_TOKEN_004';

        // Redirigir la solicitud al endpoint "juego" del GamePlatformController
        // Esto recupera los datos del ranking para el juego especificado
        $rankingResponse = $this->forward('App\\Controller\\GamePlatformController::juego', [
            'request' => new Request([], [
                'api_key' => $apiKey,
                'token_juego' => $tokenJuego
            ])
        ]);

        // Decodificar la respuesta JSON para extraer los datos del ranking
        $rankingData = json_decode($rankingResponse->getContent(), true);

        // Renderizar la plantilla game4 y pasarle los datos del ranking
        return $this->render('game4/index.html.twig', [
            'controller_name' => 'Game4Controller',
            'ranking' => $rankingData['data']['Listado jugadores'] ?? [],
        ]);
    }
}
