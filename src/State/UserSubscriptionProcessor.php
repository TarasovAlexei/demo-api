<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class UserSubscriptionProcessor implements ProcessorInterface
{
    public function __construct(
        private Security $security,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $currentUser = $this->security->getUser();

        if (!$currentUser instanceof User) {
            throw new BadRequestHttpException('Вы должны быть авторизованы');
        }
        
        $targetUser = $this->userRepository->find($uriVariables['id']);

        if (!$targetUser) {
            throw new BadRequestHttpException('Пользователь не найден');
        }

        if ($currentUser->getId() === $targetUser->getId()) {
            throw new BadRequestHttpException('Вы не можете подписаться на самого себя');
        }

        if ($operation->getName() === 'user_follow') {
            $currentUser->addFollowing($targetUser);
        } else {
            $currentUser->removeFollowing($targetUser);
        }

        $this->entityManager->flush();
    }
}

