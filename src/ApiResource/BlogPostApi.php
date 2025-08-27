<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\BlogPost;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ApiResource(
    shortName: 'Post',
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

    public ?string $createdAt = null;

    public ?bool $isMine = null;

    public ?UserApi $author = null;
}