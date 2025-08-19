<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\ItemProvider;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BlogPostStateProvider implements ProviderInterface
{   
    public function __construct(
        #[Autowire(service: ItemProvider::class)] private ProviderInterface $itemProvider,
        private Security $security,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $post = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$post instanceof BlogPost) {
            return $post;
        }

        $post->setIsAuthorByAuthenticatedUser($this->security->getUser() === $post->getAuthor());
        return $post;
    }
}
