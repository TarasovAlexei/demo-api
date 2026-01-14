<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\BlogPostApi;
use App\Entity\BlogPody;
use App\Entity\Notification;
use App\Repository\BlogPostRepository;
use Doctrine\ORM\EntityManagerInterface;

class BlogPostStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityClassDtoStateProcessor $innerProcessor,
        private EntityManagerInterface $entityManager,
        private BlogPostRepository $repository,
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $result = $this->innerProcessor->process($data, $operation, $uriVariables, $context);

        $previousData = $context['previous_data'] ?? null;
        if ($previousData instanceof BlogPostApi
            && $data->isPublished
            && $previousData->isPublished !== $data->isPublished
        ) {
            $entity = $this->repository->find($data->id);
            $notification = new Notification();
            $notification->setBlogPost($entity);
            $notification->setMessage('Post has been published!');
            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }

        return $result;
    }
}