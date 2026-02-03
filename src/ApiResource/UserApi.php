<?php

namespace App\ApiResource;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\ApiProperty;
use App\State\UserSubscriptionProcessor;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            validationContext: ['groups' => ['Default', 'postValidation']],
            security: 'is_granted("PUBLIC_ACCESS")',
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")'
        ),
        new Delete(),
        new Post(
            uriTemplate: '/users/{id}/follow',
            input: false, 
            security: 'is_granted("ROLE_USER")',
            status: 204,
            name: 'user_follow',
            processor: \App\State\UserSubscriptionProcessor::class,
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Подписаться на пользователя',
            ),
        ),
        new Post(
            uriTemplate: '/users/{id}/unfollow',
            input: false,
            security: 'is_granted("ROLE_USER")',
            status: 204,
            name: 'user_unfollow',
            processor: \App\State\UserSubscriptionProcessor::class,
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Отписаться от пользователя',
            ),
        ),
    ],
    paginationItemsPerPage: 5,
    security: 'is_granted("ROLE_USER")',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
#[ApiFilter(SearchFilter::class, properties: [
    'lastName' => 'partial',
    'id' => 'exact',
])]
class UserApi
{   
    #[ApiProperty(identifier: true, readable: true, writable: false)]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;

    #[ApiProperty(readable: false, writable: true)]
    #[Assert\NotBlank(groups: ['postValidation'])]
    public ?string $password = null;

    #[ApiProperty(writable: true)]
    public mixed $avatar = null; 

    #[ApiProperty(readable: true, writable: false)]
    public int $followersCount = 0;

    #[ApiProperty(readable: true, writable: false)]
    public int $followingCount = 0;

     /** @var array<int, self> */
    #[ApiProperty(readable: true, writable: false)]
    public array $followersPreview = [];

    /** @var array<int, self> */
    #[ApiProperty(readable: true, writable: false)]
    public array $followingPreview = [];
}
