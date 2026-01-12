<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Doctrine\Common\State\RemoveProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\DeleteOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfonycasts\MicroMapper\MicroMapperInterface;

class EntityClassDtoStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)] private ProcessorInterface $persistProcessor,
        #[Autowire(service: RemoveProcessor::class)] private ProcessorInterface $removeProcessor,
        private MicroMapperInterface $microMapper
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $stateOptions = $operation->getStateOptions();
        
        if (!$stateOptions instanceof Options) {
            throw new \LogicException('State options must be an instance of ApiPlatform\Doctrine\Orm\State\Options');
        }

        $entityClass = $stateOptions->getEntityClass();
        if (!$entityClass) {
            throw new \LogicException('Entity class must be defined in state options.');
        }

        $entity = $this->mapDtoToEntity($data, $entityClass);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);

            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);
        
        $data->id = $entity->getId();

        return $data;
    }

    private function mapDtoToEntity(object $dto, string $entityClass): object
    {
        return $this->microMapper->map($dto, $entityClass);
    }
}
