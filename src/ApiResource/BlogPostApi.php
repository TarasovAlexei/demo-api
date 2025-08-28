<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Entity\BlogPost;
use App\State\EntityClassDtoStateProcessor;
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
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        )
    ],
    paginationItemsPerPage: 10,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: BlogPost::class),
)]
class BlogPostApi
{
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[NotBlank]
    public ?string $title = null;

    #[NotBlank]
    public ?string $content = null;

    #[ApiProperty(security: 'object === null or is_granted("EDIT", object)')]
    public bool $isPublished = false;

    public ?string $createdAt = null;

    public ?bool $isMine = null;

    #[IsValidAuthor]
    public ?UserApi $author = null;
}