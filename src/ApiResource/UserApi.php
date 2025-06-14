<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\BlogPost;
use App\Entity\User;
use App\State\EntityToDtoStateProvider;
use App\State\EntityClassDtoStateProcessor;

#[ApiResource(
    shortName: 'User',
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
class UserApi
{   
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    public ?string $email = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    /**
     * The plaintext password when being set or changed.
     */
    #[ApiProperty(readable: false)]
    public ?string $password = null;

    /**
     * @var array<int, BlogPost>
     */
    #[ApiProperty(writable: false)]
    public array $blogPosts = [];

    #[ApiProperty(writable: false)]
    public int $popularity = 0;

}