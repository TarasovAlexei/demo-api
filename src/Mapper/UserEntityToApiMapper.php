<?php

namespace App\Mapper;

use App\ApiResource\MediaObjectApi;
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
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        if (!$from instanceof User) {
            throw new \InvalidArgumentException('Expected instance of User');
        }

        $dto = new UserApi();
        $dto->id = $from->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        if (!$from instanceof User || !$to instanceof UserApi) {
            throw new \InvalidArgumentException('Unexpected types for mapping');
        }

        $to->email = $from->getEmail();
        $to->firstName = $from->getFirstName();
        $to->lastName = $from->getLastName();

       $to->blogPosts = array_values(array_map(function (BlogPost $blogPost) use ($context) {
            return $this->microMapper->map($blogPost, BlogPostApi::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
            ...$context
            ]);
        }, $from->getPublishedBlogPosts()->toArray()));

         if ($from->getAvatar()) {
            $to->avatar = $this->microMapper->map(
                $from->getAvatar(), 
                MediaObjectApi::class, 
                $context
            );
        } else {
            $to->avatar = null;
        }

        return $to;
    }
}
