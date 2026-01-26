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
        private MicroMapperInterface $microMapper,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if (null === $data && $operation->getClass()) {
            $dtoClass = $operation->getClass();
            $data = new $dtoClass();
        }

        $stateOptions = $operation->getStateOptions();
        if (!$stateOptions instanceof Options || !$stateOptions->getEntityClass()) {
            throw new \LogicException(sprintf('Entity class must be defined in "stateOptions" for operation "%s".', $operation->getName()));
        }

        $entity = $this->microMapper->map($data, $stateOptions->getEntityClass(), $context);

        if ($operation instanceof DeleteOperationInterface) {
            $this->removeProcessor->process($entity, $operation, $uriVariables, $context);
            return null;
        }

        $this->persistProcessor->process($entity, $operation, $uriVariables, $context);

        return $this->microMapper->map($entity, $operation->getClass(), $context);
    }
}
