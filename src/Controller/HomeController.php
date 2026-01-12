<?php

namespace App\Controller;

use App\Repository\PartidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/account', name: 'app_account')]
    public function account(PartidaRepository $partidaRepository): Response
    {
        $user = $this->getUser();
        
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Obtener todas las partidas del usuario
        $partidas = $partidaRepository->findBy(
            ['usuario' => $user],
            ['fecha' => 'DESC']
        );

        // Calcular estadísticas
        $totalPartidas = count($partidas);
        $totalPuntos = 0;
        $mejorPuntuacion = 0;
        $juegosFrecuentes = [];

        foreach ($partidas as $partida) {
            $totalPuntos += $partida->getPuntos();
            if ($partida->getPuntos() > $mejorPuntuacion) {
                $mejorPuntuacion = $partida->getPuntos();
            }
            
            $juegoNombre = $partida->getJuego()->getNombre();
            if (!isset($juegosFrecuentes[$juegoNombre])) {
                $juegosFrecuentes[$juegoNombre] = 0;
            }
            $juegosFrecuentes[$juegoNombre]++;
        }

        $promedioPuntos = $totalPartidas > 0 ? round($totalPuntos / $totalPartidas, 2) : 0;
        
        // Ordenar juegos por frecuencia
        arsort($juegosFrecuentes);

        return $this->render('home/account.html.twig', [
            'user' => $user,
            'partidas' => $partidas,
            'totalPartidas' => $totalPartidas,
            'totalPuntos' => $totalPuntos,
            'mejorPuntuacion' => $mejorPuntuacion,
            'promedioPuntos' => $promedioPuntos,
            'juegosFrecuentes' => $juegosFrecuentes,
        ]);
    }
}
