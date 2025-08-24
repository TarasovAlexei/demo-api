<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
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
#[ApiFilter(SearchFilter::class, properties: [
    'username' => 'partial',
])]
class UserApi
{
    public ?int $id = null;

    public ?string $email = null;

    public ?string $firstName = null;

    public ?string $lastName = null;

    public ?string $username = null;

    public ?string $password = null;

    /**
     * @var array<int, BlogPost>
     */
    public array $blogPosts = [];
}