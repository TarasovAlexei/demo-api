<?php

namespace App\Mapper;

use App\ApiResource\UserApi;
use App\Entity\User;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use InvalidArgumentException;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{
    public function load(object $from, string $toClass, array $context): object
    {
        $entity = $from;
        
        if (!$from instanceof User) {
            throw new \InvalidArgumentException('Expected instance of User');
        }

        $dto = new UserApi();
        $dto->id = $entity->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        $entity = $from;
        $dto = $to;

        if (!$from instanceof User) {
            throw new \InvalidArgumentException(sprintf('Source must be "%s", "%s" given.', User::class, $from::class));
        }

        if (!$to instanceof UserApi) {
            throw new \InvalidArgumentException(sprintf('Target must be "%s", "%s" given.', UserApi::class, $to::class));
        }

        $dto->email = $entity->getEmail();
        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->blogPosts = $entity->getPublishedBlogPosts()->getValues();

        return $dto;
    }
}