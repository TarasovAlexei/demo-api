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
use Vich\UploaderBundle\Storage\StorageInterface; 

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private StorageInterface $storage, 
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
        $to->followersCount = $from->getFollowers()->count();
        $to->followingCount = $from->getFollowing()->count();

        if ($from->getAvatar()) {
            $avatarDto = $this->microMapper->map($from->getAvatar(), MediaObjectApi::class, $context);
            
            $avatarDto->contentUrl = $this->storage->resolveUri($from->getAvatar(), 'file');
            
            $to->avatar = $avatarDto;
        }

        $mapShortUser = function (User $user) use ($context) {
            return $this->microMapper->map($user, UserApi::class, [
                ...$context,
                'is_preview' => true
            ]);
        };

        if (!($context['is_preview'] ?? false)) {
            $to->followersPreview = array_map($mapShortUser, $from->getFollowers()->slice(0, 8));
            $to->followingPreview = array_map($mapShortUser, $from->getFollowing()->slice(0, 8));
        }

        return $to;
    }
}
