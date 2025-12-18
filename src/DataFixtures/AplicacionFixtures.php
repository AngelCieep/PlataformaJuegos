<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Aplicacion;

class AplicacionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Crear aplicación de prueba
        $aplicacion = new Aplicacion();
        $aplicacion->setNombre('Aplicación de Prueba');
        $aplicacion->setApiKey('ABCDEFGHIJK1234567890');
        $aplicacion->setEstado(true);
        $manager->persist($aplicacion);

        // Crear otra aplicación inactiva
        $aplicacion2 = new Aplicacion();
        $aplicacion2->setNombre('Aplicación Inactiva');
        $aplicacion2->setApiKey('INACTIVE_API_KEY_12345');
        $aplicacion2->setEstado(false);
        $manager->persist($aplicacion2);

        $manager->flush();
    }
}
