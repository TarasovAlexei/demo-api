<?php

namespace App\MessageHandler;

use App\Entity\Notification;
use App\Message\PostPublishedNotification;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class PostPublishedNotificationHandler
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private BlogPostRepository $repository,
    ) {
    }

    public function __invoke(PostPublishedNotification $message): void
    {
        $entity = $this->repository->find($message->blogPostId);

        if (!$entity) {
            return;
        }

        $existing = $this->entityManager->getRepository(Notification::class)
        ->findOneBy(['blogPost' => $entity]);

        if ($existing) {
            return; 
        }

        $notification = new Notification();
        $notification->setBlogPost($entity);
        $notification->setMessage('Post has been published!');

        $this->entityManager->persist($notification);
        $this->entityManager->flush();
    }
}
