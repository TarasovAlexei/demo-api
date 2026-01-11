<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use App\Repository\BlogPostRepository;
use Carbon\Carbon;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\IsValidAuthor;
use App\State\BlogPostStateProvider;
use App\State\BlogPostStateProcessor;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
#[ApiResource(
    shortName: 'Post',
    operations: [
        new Get(
            normalizationContext: [
                'groups' => ['post:read', 'post:item:get'],
            ],
        ),
        new GetCollection(),
        new Post(
            security: 'is_granted("ROLE_POST_CREATE")',
            processor: BlogPostStateProcessor::class,
        ),
        new Patch(
            security: 'is_granted("EDIT", object)',
            processor: BlogPostStateProcessor::class,
        ),
        new Delete(
            security: 'is_granted("ROLE_ADMIN")',
        ),
    ],
    normalizationContext: [
        'groups' => ['post:read'],
    ],
    denormalizationContext: [
        'groups' => ['post:write'],
    ],
    paginationItemsPerPage: 10,
    provider: BlogPostStateProvider::class,
    formats: [
        'jsonld',
        'json',
        'html',
        'csv' => 'text/csv',
    ],
)]
#[ApiResource(
    uriTemplate: '/users/{user_id}/posts.{_format}',
    shortName: 'Post',
    operations: [new GetCollection()],
    uriVariables: [
        'user_id' => new Link(
            fromProperty: 'blogPosts',
            fromClass: User::class,
        ),
    ],
    normalizationContext: [
        'groups' => ['post:read'],
    ],
)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['post:read', 'post:write', 'user:read', 'user:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 50, maxMessage: 'No more than 50 characters')]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['post:read', 'post:write', 'user:read', 'user:write'])]
    #[ApiFilter(SearchFilter::class, strategy: 'partial')]
    #[Assert\NotBlank]
    private ?string $content = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt;

    #[ORM\Column]
    #[ApiFilter(BooleanFilter::class)]
    #[Groups(['author:read', 'post:write'])]
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read', 'post:write'])]
    #[Assert\Valid]
    #[IsValidAuthor]
    private ?User $author = null;

    /**
     * @var bool Non-persisted property to help determine if the post is author by the authenticated user
     */
    #[Groups(['post:read'])] // Чтобы поле попало в JSON-ответ
    private bool $isAuthorByAuthenticatedUser = false; // Сразу дайте дефолтное значение    

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    #[Groups(['post:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): static
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    #[Groups(['post:read'])]
    #[SerializedName('isMine')]
    public function isAuthorByAuthenticatedUser(): bool
    {
        if (!isset($this->isAuthorByAuthenticatedUser)) {
            throw new \LogicException('You must call setIsAuthorByAuthenticatedUser() before isAuthorByAuthenticatedUser()');
        }
        return $this->isAuthorByAuthenticatedUser;
    }
    
    public function setIsAuthorByAuthenticatedUser(bool $isAuthorByAuthenticatedUser): void
    {
        $this->isAuthorByAuthenticatedUser = $isAuthorByAuthenticatedUser;
    }
}
