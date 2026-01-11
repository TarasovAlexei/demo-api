<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Entity\BlogPost;
use App\State\EntityToDtoStateProvider;
use App\Entity\User;

#[ApiResource(
    shortName: 'User',
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    stateOptions: new Options(entityClass: User::class),
)]
#[ApiFilter(SearchFilter::class, properties: [
    'lastName' => 'partial',
])]
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