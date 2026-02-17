<?php

namespace App\Mapper;

use App\ApiResource\MediaObjectApi;
use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;

#[AsMapper(from: User::class, to: UserApi::class)]
class UserEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private readonly MicroMapperInterface $microMapper,
        private readonly Security $security,
        private readonly UserRepository $userRepository,
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

        $isListView = $context['is_list_view'] ?? false;

        if (!$isListView) {
            $counts = $this->userRepository->getCounts($from->getId());
            $to->followersCount = (int) $counts['followers'];
            $to->followingCount = (int) $counts['following'];
        }

        if (isset($context['subscribed_ids'])) {
            $to->isSubscribed = in_array($from->getId(), $context['subscribed_ids']);
        } else {
            $currentUser = $this->security->getUser();
            $to->isSubscribed = ($currentUser instanceof User && $currentUser->getId() !== $from->getId())
                ? $this->userRepository->isFollowing($currentUser->getId(), $from->getId())
                : false;
        }

        if ($from->getAvatar()) {
            $to->avatar = $this->microMapper->map($from->getAvatar(), MediaObjectApi::class, $context);
        }

        return $to;
    }
}
