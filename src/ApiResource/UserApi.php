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
use App\State\UserRelationshipProvider;
use App\State\UserSubscriptionProcessor;
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(openapi: new \ApiPlatform\OpenApi\Model\Operation(tags: ['User: Profile'])),
        new GetCollection(openapi: new \ApiPlatform\OpenApi\Model\Operation(tags: ['User: Profile'])),
        new Post(
            validationContext: ['groups' => ['Default', 'postValidation']],
            security: 'is_granted("PUBLIC_ACCESS")',
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Регистрация',
                tags: ['User: Profile']
            ),
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")',
            openapi: new \ApiPlatform\OpenApi\Model\Operation(tags: ['User: Profile'])
        ),
        new Delete(openapi: new \ApiPlatform\OpenApi\Model\Operation(tags: ['User: Profile'])),
        new GetCollection(
            uriTemplate: '/users/{id}/followers',
            name: 'user_followers',
            provider: \App\State\UserRelationshipProvider::class,
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Список подписчиков',
                tags: ['User: Social Lists']
            ),
        ),
        new GetCollection(
            uriTemplate: '/users/{id}/following',
            name: 'user_following',
            provider: \App\State\UserRelationshipProvider::class,
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Список подписок',
                tags: ['User: Social Lists']
            ),
        ),
        new Post(
            uriTemplate: '/users/{id}/follow',
            input: false, 
            security: 'is_granted("ROLE_USER")',
            status: 204,
            name: 'user_follow',
            processor: \App\State\UserSubscriptionProcessor::class,
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                summary: 'Подписаться на пользователя',
                tags: ['User: Social Actions']
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
                tags: ['User: Social Actions']
            ),
        ),
    ],
    paginationItemsPerPage: 10,
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

    #[ApiProperty(readable: true, writable: false)]
    public bool $isSubscribed = false;

}
