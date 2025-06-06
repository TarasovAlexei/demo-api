<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Foundry\Test\Factories;

class UserResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

    public function testToCreateUser(): void
    {
        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'email@email.com',
                    'firstName' => 'firstName',
                    'lastName' => 'lastName',
                    'password' => 'password',
                ]
            ])
            ->assertStatus(201)
            ->post('/login', [
                'json' => [
                    'email' => 'email@email.com',
                    'password' => 'password',
                ]
            ])
            ->assertSuccessful()
        ;
    }

     public function testToUpdateUser(): void
    {
        $user = UserFactory::createOne();

       $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'lastName' => 'lastName',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200);
        }
}