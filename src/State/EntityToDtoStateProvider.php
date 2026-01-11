<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\UserApi;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class EntityToDtoStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] 
        private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)] 
        private ProviderInterface $itemProvider,
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            /** @var iterable $entities */
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);

            $dtos = [];
            foreach ($entities as $entity) {
                $dtos[] = $this->mapEntityToDto($entity);
            }

            if ($entities instanceof Paginator) {
                return new TraversablePaginator(
                    new \ArrayIterator($dtos),
                    $entities->getCurrentPage(),
                    $entities->getItemsPerPage(),
                    $entities->getTotalItems()
                );
            }

            return $dtos;
        }

        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);

        return $entity ? $this->mapEntityToDto($entity) : null;
    }

    private function mapEntityToDto(object $entity): UserApi
    {
        $dto = new UserApi();
        $dto->id = $entity->getId();
        $dto->email = $entity->getEmail();
        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->blogPosts = $entity->getPublishedBlogPosts()->getValues();

        return $dto;
    }
}
