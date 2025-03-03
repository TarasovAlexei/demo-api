<?php

namespace App\DataFixtures;

use App\Factory\PublicationFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(4);
        PublicationFactory::createMany(40, function () {
            return [
                'author' => UserFactory::random(),
            ];
        });

        $manager->flush();
    }
}
