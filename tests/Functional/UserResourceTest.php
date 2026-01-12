<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Factory\BlogPostFactory;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\ResetDatabase;

class UserResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testToCreateUser(): void
    {
        $this->browser()
            ->post('/api/users', [
                'json' => [
                    'email' => 'email@email.com',
                    'firstName' => 'FirstName',
                    'lastName' => 'LastName',
                    'password' => 'password',
                ]
            ])
            ->assertStatus(201)
            ->use(function (Json $json) {
                $json->assertMissing('password');
                $json->assertMissing('id');
            })
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
                    'lastName' => 'changed',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200);;
    }

    public function testPostsCannotBeStolen(): void
    {
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $blogPosts = BlogPostFactory::createOne(['author' => $otherUser]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'lastName' => 'changed',
                    'blogPosts' => [
                        '/api/posts/' . $blogPosts->getId(),
                    ],
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(422);
    }

    public function testUnpublishedPostsNotReturned(): void
    {
        $user = UserFactory::createOne();
        BlogPostFactory::createOne([
            'isPublished' => false,
            'author' => $user,
        ]);

        $this->browser()
            ->actingAs(UserFactory::createOne())
            ->get('/api/users/' . $user->getId())
            ->assertJsonMatches('length("blogPosts")', 0);
    }


}