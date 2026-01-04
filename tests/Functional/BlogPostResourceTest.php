<?php

namespace App\Tests\Functional;

use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class BlogPostResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testGetCollectionOfPosts(): void
    {
        BlogPostFactory::createMany(5);

        $this->browser()
            ->get('/api/posts')
            ->assertJson()
            ->assertJsonMatches('totalItems', 5)
            ->assertJsonMatches('length("member")', 5)
        ;
    }

    public function testToCreatePost(): void
    {
        $user = UserFactory::createOne();

        $this->browser()
            ->actingAs($user)
            ->post('/api/posts', [
                'json' => [],
            ])
            ->assertStatus(422)
            ->post('/api/posts', HttpOptions::json([
                    'title' => 'The title',
                    'content' => 'The content',
                    'author' => '/api/users/'.$user->getId(),
                ]))
            ->assertStatus(201)
            ->assertJsonMatches('title', 'The title')
        ;
    }
}