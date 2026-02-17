<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }

        $user->setPassword($newHashedPassword);
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
    }

    /**
     * Поиск списка подписчиков или подписок с предзагрузкой аватаров.
     */
    public function findRelationshipsPaginated(string $type, int $userId, int $offset, int $limit): array
    {
        $qb = $this->createQueryBuilder('u')
            ->leftJoin('u.avatar', 'a')->addSelect('a')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        // Определяем направление связи
        $relation = ($type === 'followers') ? 'u.following' : 'u.followers';

        $qb->innerJoin($relation, 'target')
            ->where('target.id = :userId')
            ->setParameter('userId', $userId);

        $paginator = new Paginator($qb->getQuery());

        return iterator_to_array($paginator->getIterator());
    }

    /**
     * Базовый подсчет количества для пагинации.
     */
    public function countRelationships(string $type, int $userId): int
    {
        $qb = $this->createQueryBuilder('u')->select('COUNT(u.id)');
        $relation = ($type === 'followers') ? 'u.following' : 'u.followers';

        return (int) $qb->innerJoin($relation, 'target')
            ->where('target.id = :userId')
            ->setParameter('userId', $userId)
            ->getQuery
            ->getSingleScalarResult();
    }
}
