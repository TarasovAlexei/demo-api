<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\ApiResource\UserApi;
use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: BlogPost::class, to: BlogPostApi::class)]
class BlogPostEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private Security $security,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof BlogPost);

        $dto = new BlogPostApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        assert($entity instanceof BlogPost);
        assert($dto instanceof BlogPostApi);

        $dto->title = $entity->getTitle();
        $dto->content = $entity->getContent();
        $dto->author = $this->microMapper->map($entity->getAuthor(), UserApi::class);
        $dto->createdAtAgo = $entity->getCreatedAtAgo();
        $dto->isMine = $this->security->getUser() && $this->security->getUser() === $entity->getAuthor();

        return $dto;
    }
}