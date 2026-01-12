<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Aplicacion;
use App\Entity\User;

class AplicacionFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // Obtener el usuario owner (creado por NuevoUsuario fixture)
        $owner = $manager->getRepository(User::class)->findOneBy(['email' => 'admin@scorenest.com']);
        
        if (!$owner) {
            throw new \Exception('No se encontró el usuario admin@scorenest.com. Ejecuta el fixture NuevoUsuario primero.');
        }

        // Crear aplicación de prueba
        $aplicacion = new Aplicacion();
        $aplicacion->setNombre('ScoreNest');
        $aplicacion->setApiKey('ABCDEFGHIJK1234567890');
        $aplicacion->setEstado(true);
        $aplicacion->setOwner($owner);
        $manager->persist($aplicacion);

        // Crear otra aplicación inactiva
        $aplicacion2 = new Aplicacion();
        $aplicacion2->setNombre('Aplicación Inactiva');
        $aplicacion2->setApiKey('INACTIVE_API_KEY_12345');
        $aplicacion2->setEstado(false);
        $aplicacion2->setOwner($owner);
        $manager->persist($aplicacion2);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            NuevoUsuario::class,
        ];
    }
}
