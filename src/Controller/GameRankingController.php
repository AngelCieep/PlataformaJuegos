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
        // Obtener el juego con más partidas
        $juegoConMasPartidas = $juegoRepository->createQueryBuilder('j')
            ->select('j', 'COUNT(p.id) as HIDDEN partidasCount')
            ->leftJoin('j.partidas', 'p')
            ->where('j.estado = true')
            ->groupBy('j.id')
            ->orderBy('partidasCount', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
        
        // Si hay juegos, redirigir al que tiene más partidas
        if ($juegoConMasPartidas) {
            return $this->redirectToRoute('app_game_ranking_juego', ['id' => $juegoConMasPartidas->getId()]);
        }
        
        // Si no hay juegos, mostrar página vacía
        return $this->render('game_ranking/index.html.twig', [
            'juegos' => [],
            'ranking' => [],
            'selectedJuego' => null,
            'top1PorJuego' => [],
        ]);
    }
    
    #[Route('/ranking/{id}', name: 'app_game_ranking_juego')]
    public function rankingPorJuego(
        Juego $juego,
        JuegoRepository $juegoRepository,
        PartidaRepository $partidaRepository
    ): Response
    {
        // Obtener todos los juegos activos con su cantidad de partidas, ordenados por cantidad
        $juegos = $juegoRepository->createQueryBuilder('j')
            ->select('j', 'COUNT(p.id) as HIDDEN partidasCount')
            ->leftJoin('j.partidas', 'p')
            ->where('j.estado = true')
            ->groupBy('j.id')
            ->orderBy('partidasCount', 'DESC')
            ->addOrderBy('j.nombre', 'ASC')
            ->getQuery()
            ->getResult();
        
        // Obtener el conteo de partidas por juego para pasarlo a la vista
        $partidasPorJuego = [];
        $top1PorJuego = [];
        foreach ($juegos as $juego_item) {
            $partidasPorJuego[$juego_item->getId()] = $partidaRepository->count(['juego' => $juego_item]);
            
            // Obtener el TOP 1 de este juego
            $topPartida = $partidaRepository->createQueryBuilder('p')
                ->select('p')
                ->join('p.usuario', 'u')
                ->where('p.juego = :juego')
                ->setParameter('juego', $juego_item)
                ->orderBy('p.puntos', 'DESC')
                ->addOrderBy('p.fecha', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
            
            $top1PorJuego[$juego_item->getId()] = $topPartida;
        }
        
        // Obtener la mejor puntuación de cada usuario en el juego seleccionado
        $ranking = $partidaRepository->createQueryBuilder('p')
            ->select('p')
            ->join('p.usuario', 'u')
            ->where('p.juego = :juego')
            ->andWhere('p.puntos = (
                SELECT MAX(p2.puntos) FROM App\\Entity\\Partida p2
                WHERE p2.usuario = p.usuario AND p2.juego = :juego
            )')
            ->setParameter('juego', $juego)
            ->groupBy('u.id')
            ->orderBy('p.puntos', 'DESC')
            ->addOrderBy('p.fecha', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
        
        return $this->render('game_ranking/index.html.twig', [
            'juegos' => $juegos,
            'ranking' => $ranking,
            'selectedJuego' => $juego,
            'top1PorJuego' => $top1PorJuego,
        ]);
    }
}
