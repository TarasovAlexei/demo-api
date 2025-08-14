<?php

namespace App\Tests\Functional;

use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;

class BlogPostResourceTest extends ApiTestCase
{
    use ResetDatabase;

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
                'title' => 'Title',
                'content' => 'Content',
                'author' => '/api/users/'.$user->getId(),
            ]))
            ->assertStatus(201)
            ->assertJsonMatches('title', 'Title')
        ;
    }
}