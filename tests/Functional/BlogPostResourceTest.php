<?php

namespace App\Tests\Functional;

use App\Entity\ApiToken;
use App\Factory\ApiTokenFactory;
use App\Factory\BlogPostFactory;
use App\Factory\NotificationFactory;
use App\Factory\UserFactory;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class BlogPostResourceTest extends ApiTestCase
{
    use ResetDatabase;
    use InteractsWithMessenger; 

    public function testGetCollectionOfPosts(): void
    {
        BlogPostFactory::createMany(5, [
            'isPublished' => true,
        ]);
        BlogPostFactory::createOne([
            'isPublished' => false,
        ]);

        $this->browser()
            ->get('/api/posts')
            ->assertJson()
            ->assertJsonMatches('totalItems', 5)
            ->assertJsonMatches('length("member")', 5)
        ;
    }

    public function testGetOneUnpublishedPost404s(): void
    {
        $blogPost = BlogPostFactory::createOne([
            'isPublished' => false,
        ]);

        $this->browser()
            ->get('/api/posts/'.$blogPost->getId())
            ->assertStatus(404);
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
                    'title' => 'The title',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'The title')
        ;
        

        $user2 = UserFactory::createOne();

        $this->browser()
            ->actingAs($user2)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    'title' => 'The title',
                    // be tricky and try to change the author
                    'author' => '/api/users/'.$user2->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(403)
        ;

        $this->browser()
            ->actingAs($user)
            ->patch('/api/posts/'.$post->getId(), [
                'json' => [
                    // change the author to someone else
                    'author' => '/api/users/'.$user2->getId(),
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']      
            ])
            ->assertStatus(422)
        ;
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
                    'title' => 'The title',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'The title')
        ;
    }

    public function testAuthorCanSeeIsPublishedAndIsMineFields(): void
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
                    'title' => 'The title',
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->assertJsonMatches('title', 'The title')
            ->assertJsonMatches('isPublished', true)
            ->assertJsonMatches('isMine', true)
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
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->assertJsonMatches('isPublished', true)
        ;

        $this->transport('async')->queue()->assertContains(\App\Message\PostPublishedNotification::class);
        $this->transport('async')->process();

        $this->transport('async')->queue()->assertEmpty();

        NotificationFactory::repository()->assert()->count(1);
        
        $notification = NotificationFactory::repository()->first();
        $this->assertSame('Post has been published!', $notification->getMessage());
    }
}