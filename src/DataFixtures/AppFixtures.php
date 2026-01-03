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
            'email' => 'tarasov@alexey.com',
            'password' => 'tarasov',
        ]);

        UserFactory::createMany(10);
        BlogPostFactory::createMany(40, function () {
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
