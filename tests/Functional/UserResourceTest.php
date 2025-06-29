<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use App\Factory\BlogPostFactory;
use Zenstruck\Browser\Json;
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
                    'lastName' => 'lastName',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200);
    }

    public function testPostsCanBeRemoved(): void
    {
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $blogPost = BlogPostFactory::createOne(['author' => $user]);
        BlogPostFactory::createOne(['author' => $user]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'blogPosts' => [
                        '/api/posts/' . $blogPost->getId(),
                    ],
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->get('/api/users/' . $user->getId())
            ->assertJsonMatches('length("blogPosts")', 1)
            ->assertJsonMatches('blogPosts[0]', '/api/posts/' . $blogPost->getId())
        ;
    }

    public function testPostsCannotBeStolen(): void
    {
        $user = UserFactory::createOne();
        $otherUser = UserFactory::createOne();
        $blogPost = BlogPostFactory::createOne(['author' => $otherUser]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/' . $user->getId(), [
                'json' => [
                    'firstName' => 'changed',
                    'blogPosts' => [
                        '/api/posts/' . $blogPost->getId(),
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