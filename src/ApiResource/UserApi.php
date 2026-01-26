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
use App\State\EntityClassDtoStateProcessor;
use App\State\EntityToDtoStateProvider;
use App\Entity\User;
use App\Validator\PostsAllowedAuthorChange;
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
    ],
    paginationItemsPerPage: 5,
    security: 'is_granted("ROLE_USER")',
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
)]
#[ApiFilter(SearchFilter::class, properties: [
    'lastName' => 'partial',
])]
#[PostsAllowedAuthorChange]
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

    public ?string $avatar = null;

    /**
     * @var array<int, BlogPostApi>
     */
    public array $blogPosts = [];
}
