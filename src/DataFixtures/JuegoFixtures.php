<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Juego;
use App\Entity\Aplicacion;

class JuegoFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
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
            $aplicacion->setNombre('ScoreNest');
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
        $juegoTetris->setDescription('Juego de bloques clásico. Completa líneas para ganar puntos');
        $juegoTetris->setEstado(true);
        $juegoTetris->setAplicacion($aplicacion);
        $manager->persist($juegoTetris);

        // Crear juego Tres en Raya (Game3)
        $juegoTresRaya = new Juego();
        $juegoTresRaya->setNombre('Tres en Raya');
        $juegoTresRaya->setTokenJuego('TRESRAYA_GAME_TOKEN_003');
        $juegoTresRaya->setDescription('Clásico juego de estrategia. ¡Tres en línea para ganar!');
        $juegoTresRaya->setEstado(true);
        $juegoTresRaya->setAplicacion($aplicacion);
        $manager->persist($juegoTresRaya);

        // Crear juego Ahorcado (Game4)
        $juegoAhorcado = new Juego();
        $juegoAhorcado->setNombre('Ahorcado');
        $juegoAhorcado->setTokenJuego('AHORCADO_GAME_TOKEN_004');
        $juegoAhorcado->setDescription('Adivina la palabra antes de que se acabe el tiempo');
        $juegoAhorcado->setEstado(true);
        $juegoAhorcado->setAplicacion($aplicacion);
        $manager->persist($juegoAhorcado);

        // Crear juego Memory (Game5)
        $juegoMemory = new Juego();
        $juegoMemory->setNombre('Memory Game');
        $juegoMemory->setTokenJuego('MEMORY_GAME_TOKEN_005');
        $juegoMemory->setDescription('Encuentra las parejas de cartas. Pon a prueba tu memoria');
        $juegoMemory->setEstado(true);
        $juegoMemory->setAplicacion($aplicacion);
        $manager->persist($juegoMemory);

        // Crear juego Simon Dice (Game6)
        $juegoSimon = new Juego();
        $juegoSimon->setNombre('Simon Dice');
        $juegoSimon->setTokenJuego('SIMON_GAME_TOKEN_006');
        $juegoSimon->setDescription('Repite la secuencia de colores. ¡Cada vez más difícil!');
        $juegoSimon->setEstado(true);
        $juegoSimon->setAplicacion($aplicacion);
        $manager->persist($juegoSimon);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AplicacionFixtures::class,
        ];
    }
}
