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
use Symfony\Bundle\SecurityBundle\Security;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private Security $security, 
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

          $currentUser = $this->security->getUser();
    
        if (!($context['is_preview'] ?? false) && $currentUser instanceof User) {
            $to->isSubscribed = $from->getFollowers()->contains($currentUser);
        }


        $to->followersCount = $from->getFollowers()->count();
        $to->followingCount = $from->getFollowing()->count();

       if ($from->getAvatar()) {
            $to->avatar = $this->microMapper->map($from->getAvatar(), MediaObjectApi::class, $context);
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
