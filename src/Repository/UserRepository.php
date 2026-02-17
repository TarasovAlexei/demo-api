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
     * Используется Symfony Security для обновления хеша пароля.
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
     * Получение счетчиков (followers/following) ОДНИМ запросом через Native SQL.
     * Это гораздо быстрее, чем два отдельных DQL запроса.
     */
    public function getCounts(int $userId): array
    {
        return $this->getEntityManager()->getConnection()->fetchAssociative('
            SELECT 
                (SELECT COUNT(*) FROM subscriptions WHERE user_target = :id) as followers,
                (SELECT COUNT(*) FROM subscriptions WHERE user_source = :id) as following
        ', ['id' => $userId]) ?: ['followers' => 0, 'following' => 0];
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
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Проверка подписки для одного конкретного профиля.
     */
    public function isFollowing(int $followerId, int $followedId): bool
    {
        return (bool) $this->getEntityManager()
            ->createQuery('SELECT COUNT(target.id) 
                        FROM App\Entity\User u 
                        JOIN u.following target 
                        WHERE u.id = :followerId AND target.id = :followedId')
            ->setParameters([
                'followerId' => $followerId,
                'followedId' => $followedId
            ])
            ->getSingleScalarResult();
    }

    /**
     * Получение юзера со всеми данными для профиля (включая аватар) за 1 запрос.
     */
    public function findUserForProfile(int $id): ?User
    {
        return $this->createQueryBuilder('u')
            ->leftJoin('u.avatar', 'a')->addSelect('a')
            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
