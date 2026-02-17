<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\Pagination;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class UserRelationshipProvider implements ProviderInterface
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly MicroMapperInterface $microMapper,
        private readonly Pagination $pagination,
        private readonly Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $userId = (int) ($uriVariables['id'] ?? throw new NotFoundHttpException('User ID is required'));

        $type = match ($operation->getName()) {
            'user_followers' => 'followers',
            'user_following' => 'following',
            default => throw new \RuntimeException(sprintf('Unknown operation "%s"', $operation->getName())),
        };

        [$page, $offset, $limit] = $this->pagination->getPagination($operation, $context);
        
        $totalItems = $this->userRepository->countRelationships($type, $userId);
        
        if ($totalItems === 0) {
            return new TraversablePaginator(new \ArrayIterator([]), $page, $limit, 0);
        }

        $entities = $this->userRepository->findRelationshipsPaginated($type, $userId, $offset, $limit);

        $currentUser = $this->security->getUser();
        $subscribedIds = ($currentUser instanceof User) 
            ? $this->userRepository->getFollowingIdsInList($currentUser->getId(), array_map(fn(User $u) => $u->getId(), $entities))
            : [];

        $mappedResults = array_map(
            fn(User $entity) => $this->microMapper->map($entity, UserApi::class, [
                ...$context,
                'is_list_view' => true,
                'subscribed_ids' => $subscribedIds,
            ]),
            $entities
        );

        return new TraversablePaginator(new \ArrayIterator($mappedResults), $page, $limit, $totalItems);
    }
}
