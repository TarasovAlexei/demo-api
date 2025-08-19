<?php

namespace App\Tests\Functional;

use App\Entity\ApiToken;
use App\Factory\ApiTokenFactory;
use App\Factory\BlogPostFactory;
use App\Factory\UserFactory;
use App\Factory\NotificationFactory;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\Factories;

class BlogPostResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use Factories;

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

    public function testToCreatePostWithApiKey(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_POST_CREATE]
        ]);

        $this->browser()
            ->post('/api/posts', [
                'json' => [],
                'headers' => [
                    'Authorization' => 'Bearer '.$token->getToken()
                ]
            ])
            ->assertStatus(422)
        ;
    }

    public function testToCreatePostDeniedWithoutScope(): void
    {
        $token = ApiTokenFactory::createOne([
            'scopes' => [ApiToken::SCOPE_POST_EDIT]
        ]);

        $this->browser()
            ->post('/api/posts', [
                'json' => [],
                'headers' => [
                    'Authorization' => 'Bearer '.$token->getToken()
                ]
            ])
            ->assertStatus(403)
        ;
    }

    public function testToUpdatePost()
    {
        $user = UserFactory::createOne();
        $post = BlogPostFactory::createOne(['author' => $user]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'title' => '12345',
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', '12345')
        ;


        $user2 = UserFactory::createOne();

        $this->browser()
            ->actingAs($user2)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'title' => '6789',
                    // be tricky and try to change the author
                    'author' => '/api/users/'.$user2->getId(),
                ],
            ])
            ->assertStatus(403)
        ;

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    // change the owner to someone else
                    'author' => '/api/users/'.$user2->getId(),
                ],
            ])
            ->assertStatus(422)
        ;
    }

    public function testAuthorCanSeeIsPublishedAndIsMineField(): void
    {
        $user = UserFactory::new()->create();

        $post = BlogPostFactory::createOne([
            'isPublished' => true,
            'author' => $user,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'title' => 'Title',
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'Title')
            ->assertJsonMatches('isPublished', true)
            ->assertJsonMatches('isMine', true)
        ;
    }

    public function testOneUnpublishedPost404s(): void
    {
        $blogPost = BlogPostFactory::createOne([
            'isPublished' => false,
        ]);
        $this->browser()
            ->get('/api/posts/'.$blogPost->getId())
            ->assertStatus(404);
    }

    public function testUnpublishedWorks()
    {
        $user = UserFactory::createOne();
        $post = BlogPostFactory::createOne([
            'author' => $user,
            'isPublished' => false,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'title' => 'newTitle',
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'newTitle')
        ;
    }

    public function testPublishPost(): void
    {
        $user = UserFactory::createOne();
        $post = BlogPostFactory::createOne([
            'author' => $user,
            'isPublished' => false,
        ]);

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'isPublished' => true,
                ],
            ])
            ->assertStatus(200)
            ->assertJsonMatches('isPublished', true)
        ;

        NotificationFactory::repository()->assert()->count(1);
    }
}