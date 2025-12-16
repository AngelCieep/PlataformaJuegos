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
        $plainPassword = '12345678';
        $user = new User();
        $user->setEmail($email);
        $user->setNombre($nombre);
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        // Encriptación de la contraseña
        $hashed = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashed);
        $user->setToken(bin2hex(random_bytes(32)));
        // Guarda el registro en la base de datos.
        $manager->persist($user);
        $manager->flush();

    }
}
