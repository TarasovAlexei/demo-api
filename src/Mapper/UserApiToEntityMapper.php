<?php

namespace App\Mapper;

use App\ApiResource\UserApi;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserApi::class, to: User::class)]
final readonly class UserApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MicroMapperInterface $microMapper,
        private PropertyAccessorInterface $propertyAccessor,
    ) {
    }

    public function load(object $from, string $toClass, array $context): User
    {
        if (!$from instanceof UserApi) {
            throw new \InvalidArgumentException(sprintf('Expected "%s", got "%s"', UserApi::class, $from::class));
        }

        if (!$from->id) {
            return new User();
        }

        return $this->userRepository->find($from->id) 
            ?? throw new NotFoundHttpException("User with ID $from->id not found");
    }

    public function populate(object $from, object $to, array $context): User
    {
        
        if (!$from instanceof UserApi || !$to instanceof User) {
            throw new \InvalidArgumentException('Invalid source or target object type');
        }

        $to->setEmail($from->email);
        $to->setFirstName($from->firstName);
        $to->setLastName($from->lastName);

        if ($from->password) {
            $to->setPassword(
                $this->userPasswordHasher->hashPassword($to, $from->password)
            );
        }

        $blogPostEntities = [];
        foreach ($from->blogPosts as $blogPostApi) {
            $blogPostEntities[] = $this->microMapper->map($blogPostApi, BlogPost::class, [
                MicroMapperInterface::MAX_DEPTH => 0,
            ]);
        }
        $this->propertyAccessor->setValue($to, 'blogPosts', $blogPostEntities);

        return $to;
    }
}
