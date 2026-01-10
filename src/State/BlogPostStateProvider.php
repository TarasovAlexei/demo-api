<?php

namespace App\State;

use ApiPlatform\Doctrine\Orm\State\CollectionProvider;
use ApiPlatform\Metadata\CollectionOperationInterface;
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
        #[Autowire(service: CollectionProvider::class)] private ProviderInterface $collectionProvider,
        private Security $security,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            /** @var $paginator iterable<BlogPost> */
            $paginator = $this->collectionProvider->provide($operation, $uriVariables, $context);

            foreach ($paginator as $post) {
                $post->setIsAuthorByAuthenticatedUser($this->security->getUser() === $post->getAuthor());
            }

            return $paginator;
        }

        $post = $this->itemProvider->provide($operation, $uriVariables, $context);

        if (!$post instanceof BlogPost) {
            return $post;
        }

        $post->setIsAuthorByAuthenticatedUser($this->security->getUser() === $post->getAuthor());

        return $post;
    }
}