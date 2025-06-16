<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: BlogPostApi::class, to: BlogPost::class)]
class BlogPostApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private BlogPostRepository $repository,
    )
    {

    }

    public function load(object $from, string $toClass, array $context): object
    {
        $dto = $from;

        assert($dto instanceof BlogPostApi);

        $entity = $dto->id ? $this->repository->find($dto->id) : new BlogPost($dto->title);
        if (!$entity) {
            throw new \Exception('BlogPost not found');
        }
        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $dto = $from;
        $entity = $to;

        assert($dto instanceof BlogPostApi);
        assert($entity instanceof BlogPost);

        $entity->setContent($dto->content);

        // TODO: set published
        return $entity;

    }
}