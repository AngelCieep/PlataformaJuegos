<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Juego;
use App\Entity\Aplicacion;

class JuegoFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['Juego'];
    }

    public function load(ObjectManager $manager): void
    {
        // Obtener la aplicación de prueba
        $aplicacion = $manager->getRepository(Aplicacion::class)->findOneBy(['apiKey' => 'ABCDEFGHIJK1234567890']);
        
        if (!$aplicacion) {
            // Si no existe, crear una nueva aplicación
            $aplicacion = new Aplicacion();
            $aplicacion->setNombre('Aplicación de Prueba');
            $aplicacion->setApiKey('ABCDEFGHIJK1234567890');
            $aplicacion->setEstado(true);
            $manager->persist($aplicacion);
        }

        // Crear juego Snake (Game1)
        $juegoSnake = new Juego();
        $juegoSnake->setNombre('Snake Game');
        $juegoSnake->setTokenJuego('SNAKE_GAME_TOKEN_001');
        $juegoSnake->setDescription('Juego clásico de la serpiente donde debes comer manzanas y crecer sin chocar');
        $juegoSnake->setEstado(true);
        $juegoSnake->setAplicacion($aplicacion);
        $manager->persist($juegoSnake);

        // Crear juego Tetris (Game2)
        $juegoTetris = new Juego();
        $juegoTetris->setNombre('Tetris');
        $juegoTetris->setTokenJuego('TETRIS_GAME_TOKEN_002');
        $juegoTetris->setDescription('Juego de bloques clásico');
        $juegoTetris->setEstado(true);
        $juegoTetris->setAplicacion($aplicacion);
        $manager->persist($juegoTetris);

        // Crear juego Memory (Game3)
        $juegoMemory = new Juego();
        $juegoMemory->setNombre('Memory Game');
        $juegoMemory->setTokenJuego('MEMORY_GAME_TOKEN_003');
        $juegoMemory->setDescription('Juego de memoria con cartas');
        $juegoMemory->setEstado(true);
        $juegoMemory->setAplicacion($aplicacion);
        $manager->persist($juegoMemory);

        // Crear juego Pong (Game4)
        $juegoPong = new Juego();
        $juegoPong->setNombre('Pong');
        $juegoPong->setTokenJuego('PONG_GAME_TOKEN_004');
        // Juego clásico de ping pong con ranking y puntuación de usuarios.
        $juegoPong->setDescription('Juego clásico de ping pong con ranking y puntuación de usuarios.');
        $juegoPong->setEstado(true);
        $juegoPong->setAplicacion($aplicacion);
        $manager->persist($juegoPong);

        // Crear juego Space Invaders (Game5)
        $juegoSpace = new Juego();
        $juegoSpace->setNombre('Space Invaders');
        $juegoSpace->setTokenJuego('SPACE_GAME_TOKEN_005');
        $juegoSpace->setDescription('Defiende la tierra de invasores espaciales');
        $juegoSpace->setEstado(true);
        $juegoSpace->setAplicacion($aplicacion);
        $manager->persist($juegoSpace);

        // Crear juego Breakout (Game6)
        $juegoBreakout = new Juego();
        $juegoBreakout->setNombre('Breakout');
        $juegoBreakout->setTokenJuego('BREAKOUT_GAME_TOKEN_006');
        $juegoBreakout->setDescription('Rompe todos los bloques con la pelota');
        $juegoBreakout->setEstado(true);
        $juegoBreakout->setAplicacion($aplicacion);
        $manager->persist($juegoBreakout);

        // Crear juego Simon (Game7) para ranking público
        $juegoSimon = new Juego();
        $juegoSimon->setNombre('Simon Dice');
        $juegoSimon->setTokenJuego('SIMON_GAME_TOKEN_007');
        $juegoSimon->setDescription('Simon dice - memoriza secuencias y sube niveles');
        $juegoSimon->setEstado(true);
        $juegoSimon->setAplicacion($aplicacion);
        $manager->persist($juegoSimon);

        $manager->flush();
    }
}
