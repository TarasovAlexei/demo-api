<?php

namespace App\Tests\Functional;

use App\Factory\UserFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use App\Factory\BlogPostFactory;

class BlogPostResourceTest extends KernelTestCase
{
    use HasBrowser;
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
            ->post('/api/posts', [
                'json' => [
                    'title' => 'Title',
                    'content' => 'Content',
                    'author' => '/api/users/'.$user->getId(),
                ],
            ])
            ->assertStatus(201)
            ->assertJsonMatches('title', 'Title')
        ;
    }
}