<?php

namespace App\DataFixtures;

use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        BlogPostFactory::createMany(40, function () {
            return [
                'author' => UserFactory::random(),
            ];
        });
    }
}
