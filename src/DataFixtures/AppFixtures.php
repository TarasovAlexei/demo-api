<?php

namespace App\DataFixtures;

use App\Factory\BlogPostFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        BlogPostFactory::createMany(40);

    }
}
