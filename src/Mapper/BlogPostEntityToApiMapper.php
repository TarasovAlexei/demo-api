<?php

namespace App\Mapper;

use App\ApiResource\BlogPostApi;
use App\ApiResource\UserApi;
use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Symfonycasts\MicroMapper\MicroMapperInterface;
use InvalidArgumentException;

#[AsMapper(from: BlogPost::class, to: BlogPostApi::class)]
class BlogPostEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private MicroMapperInterface $microMapper,
        private Security $security,
    )
    {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        if (!$from instanceof BlogPost) {
            throw new InvalidArgumentException('Source object must be an instance of BlogPost');
        }

        $dto = new BlogPostApi();
        $dto->id = $from->getId();

        return $dto;
    }

    public function populate(object $from, object $to, array $context): object
    {
        if (!$from instanceof BlogPost) {
            throw new InvalidArgumentException('Source object must be an instance of BlogPost');
        }

        if (!$to instanceof BlogPostApi) {
            throw new InvalidArgumentException('Target object must be an instance of BlogPostApi');
        }

        $to->title = $from->getTitle();
        $to->content = $from->getContent();
        $to->author = $this->microMapper->map($from->getAuthor(), UserApi::class, [
            MicroMapperInterface::MAX_DEPTH => 0,
        ]);
        $to->createdAtAgo = $from->getCreatedAtAgo();
        $to->isPublished = $from->getIsPublished();
        $to->isMine = $this->security->getUser() && $this->security->getUser() === $from->getAuthor();

        return $to;
    }
}
