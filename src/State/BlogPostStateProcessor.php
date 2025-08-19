<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class BlogPostStateProcessor implements ProcessorInterface
{   
    public function __construct(
        #[Autowire(service: PersistProcessor::class)]
        private ProcessorInterface $innerProcessor,
        private Security $security
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof BlogPost && $data->getAuthor() === null && $this->security->getUser()) {
            $data->setAuthor($this->security->getUser());
        }

        if ($data instanceof BlogPost) {
            $data->setIsAuthorByAuthenticatedUser($data->getAuthor() === $this->security->getUser());
        }

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);

    }
}
