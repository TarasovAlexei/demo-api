<?php

namespace App\DataFixtures;

use App\Factory\ApiTokenFactory;
use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {   
        UserFactory::createOne([
            'email' => 'jane@symfony.com',
            'password' => 'kitten',
        ]);

        UserFactory::createMany(10);

        BlogPostFactory::createMany(40, function () {
            return [
                'author' => UserFactory::random(),
                'isPublished' => rand(0, 10) > 3,

            ];
        });

        ApiTokenFactory::createMany(30, function () {
            return [
                'ownedBy' => UserFactory::random(),
            ];
        });

    }
}
