<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // // Crear usuario de ejemplo
        // $user = new User();
        // $user->setEmail('admin@example.com');
        // $user->setNombre('Admin');
        // $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        // // Encriptación del token
        // $hashed = $this->passwordHasher->hashPassword($user, 'admin123');
        // $user->setToken($hashed);
        // $manager->persist($user);

        // $manager->flush();
    }
}
