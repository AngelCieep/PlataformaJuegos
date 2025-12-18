<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class NuevoUsuario extends Fixture implements FixtureGroupInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getGroups(): array
    {
        return ['Usuario'];
    }

    public function load(ObjectManager $manager): void
    {
        $email = 'test@email';
        $nombre = 'Txus';
        $password = '12345678';
        $user = new User();
        $user->setEmail($email);
        $user->setNombre($nombre);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        // Encriptación del token
        $hashed = $this->passwordHasher->hashPassword($user, $password);
        $user->setToken($hashed);
        // Guarda el registro en la base de datos.
        $manager->persist($user);
        $manager->flush();
    }
}
