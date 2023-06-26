<?php

namespace App\DataFixtures;

use App\Entity\Link;
use App\Entity\User;
use App\Service\LinkStatus;
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
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setRoles(['ROLE_USER']);
            $user->setLogin('user' . $i);
            $user->setPassword('');
            $manager->persist($user);

            $user->setPassword($this->passwordHasher->hashPassword($user, 12345));

            $admin = new User();
            $admin->setRoles(['ROLE_ADMIN']);
            $admin->setLogin('admin' . $i);
            $admin->setPassword('');
            $manager->persist($admin);

            $admin->setPassword($this->passwordHasher->hashPassword($admin, 12345));

            if ($i == 0) {
                for ($j = 0; $j < 20; $j++) {
                    $link = new Link(
                        $user,
                        'link_name_' . rand(1, 1000000),
                        'http://localhost:8051/click/randomtext' . rand(1, 1000000)
                    );
                    $link->setStatus(LinkStatus::MODERATION->value);
                    $manager->persist($link);


                    $link = new Link(
                        $admin,
                        'link_name_' . rand(1, 1000000),
                        'http://localhost:8051/click/randomtext' . rand(1, 1000000)
                    );
                    $link->setStatus(LinkStatus::MODERATION->value);
                    $manager->persist($link);
                }
            }
        }

        $manager->flush();
    }
}
