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

        $allUsers = $manager->getRepository(\App\Entity\User::class)->findAll();

        foreach ($allUsers as $user) {
            foreach ($allUsers as $friend) {
                if ($user->getEmail() !== $friend->getEmail()) {
                    $user->addFollowing($friend);
                }
            }
            $manager->persist($user);
        }

        $manager->flush();

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
