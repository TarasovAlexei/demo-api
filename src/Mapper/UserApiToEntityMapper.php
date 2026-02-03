<?php

namespace App\Mapper;

use ApiPlatform\Metadata\IriConverterInterface;
use App\ApiResource\UserApi;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Entity\MediaObject;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: UserApi::class, to: User::class)]
final readonly class UserApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $userPasswordHasher,
        private MicroMapperInterface $microMapper,        private IriConverterInterface $iriConverter,
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

        if ($from->avatar) {
            $iri = is_string($from->avatar) 
                ? $from->avatar 
                : ($from->avatar instanceof \App\ApiResource\MediaObjectApi ? "/api/media_objects/{$from->avatar->id}" : null);

            if ($iri) {
                try {
                    $resource = $this->iriConverter->getResourceFromIri($iri);
                    if ($resource instanceof MediaObject) {
                        $to->setAvatar($resource);
                    }
                } catch (\Exception) {
                }
            }
        } elseif (property_exists($from, 'avatar')) {
            $to->setAvatar(null);
        }

        return $to;
    }
}
