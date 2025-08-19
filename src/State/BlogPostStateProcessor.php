<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BlogPost;
use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BlogPostStateProcessor implements ProcessorInterface
{   
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $innerProcessor,
        private Security $security,
        private EntityManagerInterface $entityManager
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        assert($data instanceof BlogPost);
        $data->setAuthor($this->security->getUser());

        $data->setIsAuthorByAuthenticatedUser($data->getAuthor() === $this->security->getUser());

        $data = $this->innerProcessor->process($data, $operation, $uriVariables, $context);

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

        return $data;
    }

}
