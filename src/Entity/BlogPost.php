<?php

namespace App\Entity;

use App\Repository\BlogPostRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BlogPostRepository::class)]
class BlogPost
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $isPublished = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'blogPosts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

     /**
     * @var bool Non-persisted property to help determine if the post is author by the authenticated user
     */
    private bool $isAuthorByAuthenticatedUser = false;


    public function __construct(string $title = null)
    {   
        $this->title = $title;
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

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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
