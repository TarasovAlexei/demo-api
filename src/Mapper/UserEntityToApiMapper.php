<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\ApiResource\UserApi;
use App\Entity\BlogPost;
use App\Entity\User;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{   
    public function __construct(
        private MicroMapperInterface $microMapper,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        assert($entity instanceof User);

        $dto = new UserApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;
        
        assert($entity instanceof User);
        assert($dto instanceof UserApi);

        $dto->email = $entity->getEmail();
        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->blogPosts = array_map(function(BlogPost $blogPost) {
            return $this->microMapper->map($blogPost, BlogPostApi::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]);
        }, $entity->getPublishedBlogPosts()->getValues());
        $dto->popularity = rand(1, 100);

        return $dto;
    }
}