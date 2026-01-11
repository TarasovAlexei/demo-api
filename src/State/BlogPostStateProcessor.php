<?php

namespace App\State;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\BlogPost;
use App\Entity\User;
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
        private Security $security
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

        return $this->innerProcessor->process($data, $operation, $uriVariables, $context);
    }
}
