<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;


#[AsMapper(from: BlogPostApi::class, to: BlogPost::class)]
class BlogPostApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private BlogPostRepository $repository,
        private Security $security,
        private MicroMapperInterface $microMapper,
    )
    {

    }

    public function load(object $from, string $toClass, array $context): object
    {   
        $dto = $from;

        assert($dto instanceof BlogPostApi);

        $entity = $dto->id ? $this->repository->find($dto->id) : new BlogPost();
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

        if ($dto->author) {
            $entity->setAuthor($this->microMapper->map($dto->author, User::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]));
        } else {
            $entity->setAuthor($this->security->getUser());
        }

        $entity->setTitle($dto->title);
        $entity->setContent($dto->content);
        $entity->setIsPublished($dto->isPublished);

        return $entity;

    }
}