<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use ApiPlatform\State\Pagination\TraversablePaginator;
use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use App\ApiResource\UserApi;


class EntityToDtoStateProvider implements ProviderInterface
{   
    public function __construct(
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $entities = $this->collectionProvider->provide($operation, $uriVariables, $context);
        assert($entities instanceof Paginator);

        $dtos = [];
        foreach ($entities as $entity) {
            $dtos[] = $this->mapEntityToDto($entity);
        }
        
        return new TraversablePaginator(
            new \ArrayIterator($dtos),
            $entities->getCurrentPage(),
            $entities->getItemsPerPage(),
            $entities->getTotalItems()
        );
    }

    private function mapEntityToDto(object $entity): object
    {
        $dto = new UserApi();

        $dto->id = $entity->getId();
        $dto->email = $entity->getEmail();
        $dto->firstName = $entity->getFirstName();
        $dto->lastName = $entity->getLastName();
        $dto->blogPosts = $entity->getBlogPosts()->toArray();
        $dto->flameThrowingDistance = rand(1, 10);

        return $dto;
    }
}
