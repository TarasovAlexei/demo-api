<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use App\Entity\BlogPost;
use App\State\BlogPostStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Validator\IsValidAuthor;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Post',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_POST_CREATE")',
        ),
        new Patch(
            security: 'is_granted("EDIT", object)',
        ),
        new Delete()
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: BlogPostStateProcessor::class,
    stateOptions: new Options(entityClass: BlogPost::class),
)]
#[ApiFilter(SearchFilter::class, properties: ['author' => 'exact'])]
class BlogPostApi
{
    #[ApiProperty(readable: true, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $title = null;

    #[NotBlank]
    public ?string $content = null;

    public ?string $createdAtAgo = null;

    public bool $isMine = false;

    #[IsValidAuthor]
    public ?UserApi $author = null;

    #[ApiProperty(security: 'object === null or is_granted("EDIT", object)')]
    public bool $isPublished = false;
}