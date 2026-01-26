<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\State\ProviderInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class EntityToDtoStateProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        #[Autowire(service: ItemProvider::class)] private ProviderInterface $itemProvider,
        private MicroMapperInterface $microMapper
    ) {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $resourceClass = $operation->getClass();

        if ($operation instanceof CollectionOperationInterface) {
            $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);

            if ($entities instanceof Paginator) {
                $dtos = [];
                foreach ($entities as $entity) {
                    $dtos[] = $this->microMapper->map($entity, $resourceClass, $context);
                }

                return new TraversablePaginator(
                    new \ArrayIterator($dtos),
                    $entities->getCurrentPage(),
                    $entities->getItemsPerPage(),
                    $entities->getTotalItems()
                );
            }

            $entitiesArray = is_array($entities) ? $entities : iterator_to_array($entities);
            
            return array_map(
                fn($entity) => $this->microMapper->map($entity, $resourceClass, $context),
                $entitiesArray
            );
        }

        $entity = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$entity) {
            return null;
        }

        return $this->microMapper->map($entity, $resourceClass, $context);
    }
}
