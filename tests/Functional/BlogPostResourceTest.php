<?php

namespace App\Tests\Functional;

use App\Factory\BlogPostFactory;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\ResetDatabase;

class BlogPostResourceTest extends KernelTestCase
{
    use HasBrowser;
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
}