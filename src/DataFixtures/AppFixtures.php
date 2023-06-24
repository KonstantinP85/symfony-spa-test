<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER']);
        $user->setLogin('user');
        $user->setPassword('');
        $manager->persist($user);

        $user->setPassword($this->passwordHasher->hashPassword($user, 12345));

        $admin = new User();
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setLogin('admin');
        $admin->setPassword('');
        $manager->persist($admin);

        $admin->setPassword($this->passwordHasher->hashPassword($admin, 12345));

        $manager->flush();
    }
}
