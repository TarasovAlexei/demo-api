<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\PublicationFactory;
use App\Factory\UserFactory;
use App\Factory\ApiTokenFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{   
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        UserFactory::createOne([
            'email' => 'jane@symfony.com',
            'password' => 'kitten',
            'firstName' => 'Jane',
            'lastName' => 'Die',
        ]);
        
        UserFactory::createMany(4);

        PublicationFactory::createMany(40, function () {
            return [
                'author' => UserFactory::random(),
            ];
        });

        ApiTokenFactory::createMany(30, function () {
            return [
                'ownedBy' => UserFactory::random(),
            ];
        });


    }
}
