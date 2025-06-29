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
use App\Entity\User;
use App\State\EntityToDtoStateProvider;
use App\State\EntityClassDtoStateProcessor;
use App\Validator\PostsAllowedAuthorChange;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'User',
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            security: 'is_granted("PUBLIC_ACCESS")',
            validationContext: ['groups' => ['Default', 'postValidation']],
        ),
        new Patch(
            security: 'is_granted("ROLE_USER_EDIT")'
        ),
        new Delete(),
    ],
    paginationItemsPerPage: 5,
    provider: EntityToDtoStateProvider::class,
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: User::class),
    security: 'is_granted("ROLE_USER")',
)]
#[PostsAllowedAuthorChange]
class UserApi
{   
    #[ApiProperty(readable: false, writable: false, identifier: true)]
    public ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    public ?string $email = null;

    #[Assert\NotBlank]
    public ?string $firstName = null;

    #[Assert\NotBlank]
    public ?string $lastName = null;

    /**
     * The plaintext password when being set or changed.
     */
    #[ApiProperty(readable: false)]
    #[Assert\NotBlank(groups: ['postValidation'])]
    public ?string $password = null;

    /**
     * @var array<int, BlogPostApi>
     */
    public array $blogPosts = [];

    #[ApiProperty(writable: false)]
    public int $popularity = 0;

}