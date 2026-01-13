<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\Entity\BlogPost;
use App\Repository\BlogPostRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: BlogPostApi::class, to: BlogPost::class)]
class BlogPostApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private BlogPostRepository $repository,
        private Security $security,
    ) {}

    public function load(object $from, string $toClass, array $context): object
    {
        /** @var BlogPostApi $dto */
        $dto = $from;
        
        if (!$dto instanceof BlogPostApi) {
            throw new \InvalidArgumentException('Source object must be BlogPostApi');
        }

        $entity = $dto->id ? $this->repository->find($dto->id) : new BlogPost();

        if (!$entity) {
            throw new \RuntimeException(sprintf('BlogPost with ID "%s" not found', $dto->id));
        }

        return $entity;
    }

    public function populate(object $from, object $to, array $context): object
    {
        /** @var BlogPostApi $dto */
        $dto = $from;
        /** @var BlogPost $entity */
        $entity = $to;

        assert($dto instanceof BlogPostApi);
        assert($entity instanceof BlogPost);

        if (!$entity->getId()) {
            $user = $this->security->getUser();
            if ($user) {
                $entity->setAuthor($user);
            }
        }

        $entity->setTitle($dto->title);
        $entity->setContent($dto->content);
        $entity->setIsPublished($dto->isPublished);

        return $entity;
    }
}
