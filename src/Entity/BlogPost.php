<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
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
use Symfony\Component\Serializer\Annotation\Groups;

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
        new Post(),
        new Patch(),
        new Delete(),
    ],
    normalizationContext: [
        'groups' => ['post:read'],
    ],
    denormalizationContext: [
        'groups' => ['post:write'],
    ],
    paginationItemsPerPage: 10,
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
    private ?bool $isPublished = false;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['post:read', 'post:write'])]
    private ?User $author = null;

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
}
