<?php

namespace App\Mapper;

use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: UserApi::class, to: User::class)]
final readonly class UserApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
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

        return $to;
    }
}
