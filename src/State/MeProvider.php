<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class MeProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private MicroMapperInterface $microMapper
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            return null; 
        }

        return $this->microMapper->map($user, $operation->getClass(), $context);
    }
}


