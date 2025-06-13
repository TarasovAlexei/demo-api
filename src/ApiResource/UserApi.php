<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\BlogPost;
use App\Entity\User;
use App\State\EntityToDtoStateProvider;

#[ApiResource(
    shortName: 'User',
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)]
class UserApi
{
    public ?int $id = null;

    public ?string $email = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    /**
     * @var array<int, BlogPost>
     */
    public array $blogPosts = [];
}