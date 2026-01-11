<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BlogPost;
use App\Entity\User;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<BlogPost, BlogPost>
 */
class BlogPostStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $innerProcessor,
        private Security $security,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * @param BlogPost $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): BlogPost
    {
        if (!$data instanceof BlogPost) {
            return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
        }

        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($user && null === $data->getAuthor()) {
            $data->setAuthor($user);
        }

        $data->setIsAuthorByAuthenticatedUser(null !== $user && $data->getAuthor() === $user);

        $previousData = $context['previous_data'] ?? null;
        if ($previousData instanceof BlogPost
            && $data->getIsPublished()
            && $previousData->getIsPublished() !== $data->getIsPublished()
        ) {
            $notification = new Notification();
            $notification->setBlogPost($data);
            $notification->setMessage('Post has been published!');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
