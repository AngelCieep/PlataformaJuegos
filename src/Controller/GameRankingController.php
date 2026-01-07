<?php

namespace App\Controller;

use App\Entity\Juego;
use App\Entity\Partida;
use App\Repository\JuegoRepository;
use App\Repository\PartidaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class GameRankingController extends AbstractController
{
    #[Route('/ranking', name: 'app_game_ranking')]
    public function index(
        JuegoRepository $juegoRepository,
        PartidaRepository $partidaRepository
    ): Response
    {
        // Obtener todos los juegos activos
        $juegos = $juegoRepository->findBy(['estado' => true], ['nombre' => 'ASC']);
        
        // Obtener todas las partidas ordenadas por puntos (ranking global)
        $ranking = $partidaRepository->createQueryBuilder('p')
            ->select('p')
            ->join('p.usuario', 'u')
            ->join('p.juego', 'j')
            ->orderBy('p.puntos', 'DESC')
            ->addOrderBy('p.fecha', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
        
        return $this->render('game_ranking/index.html.twig', [
            'juegos' => $juegos,
            'ranking' => $ranking,
            'selectedJuego' => null,
        ]);
    }
    
    #[Route('/ranking/{id}', name: 'app_game_ranking_juego')]
    public function rankingPorJuego(
        Juego $juego,
        JuegoRepository $juegoRepository,
        PartidaRepository $partidaRepository
    ): Response
    {
        // Obtener todos los juegos activos
        $juegos = $juegoRepository->findBy(['estado' => true], ['nombre' => 'ASC']);
        
        // Obtener el ranking del juego seleccionado
        $ranking = $partidaRepository->createQueryBuilder('p')
            ->select('p')
            ->join('p.usuario', 'u')
            ->where('p.juego = :juego')
            ->setParameter('juego', $juego)
            ->orderBy('p.puntos', 'DESC')
            ->addOrderBy('p.fecha', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
        
        return $this->render('game_ranking/index.html.twig', [
            'juegos' => $juegos,
            'ranking' => $ranking,
            'selectedJuego' => $juego,
        ]);
    }
}
