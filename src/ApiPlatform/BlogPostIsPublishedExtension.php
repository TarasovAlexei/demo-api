<?php

namespace App\ApiPlatform;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\BlogPost;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class BlogPostIsPublishedExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{   
    public function __construct(private Security $security)
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, Operation $operation = null, array $context = []): void
    {
        $this->addIsPublishedWhere($resourceClass, $queryBuilder);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, Operation $operation = null, array $context = []): void
    {
        $this->addIsPublishedWhere($resourceClass, $queryBuilder);
    }

    /**
     * @param string $resourceClass
     * @param QueryBuilder $queryBuilder
     * @return void
     */
    private function addIsPublishedWhere(string $resourceClass, QueryBuilder $queryBuilder): void
    {
        if (BlogPost::class !== $resourceClass) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $user = $this->security->getUser();

        if ($user) {
            $queryBuilder->andWhere(sprintf('%s.isPublished = :isPublished OR %s.author = :author', $rootAlias, $rootAlias))
                ->setParameter('author', $user);
        } else {
            $queryBuilder->andWhere(sprintf('%s.isPublished = :isPublished', $rootAlias));
        }

        $queryBuilder->setParameter('isPublished', true);

    }
}