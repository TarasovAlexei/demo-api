<?php

namespace App\Mapper;

use App\ApiResource\MediaObjectApi;
use App\Entity\MediaObject;
use Liip\ImagineBundle\Imagine\Cache\CacheManager; 
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;
use Vich\UploaderBundle\Storage\StorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


#[AsMapper(from: MediaObject::class, to: MediaObjectApi::class)]
class MediaObjectEntityToApiMapper implements MapperInterface
{
    public function __construct(
        private StorageInterface $storage,
        private CacheManager $cacheManager 
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        if (!$from instanceof MediaObject) {
            throw new \InvalidArgumentException(sprintf('Source must be "%s", "%s" given.', MediaObject::class, $from::class));
        }

        $dto = new MediaObjectApi();
        $dto->id = $from->getId();

        return $dto;
    }


    public function populate(object $from, object $to, array $context): object
    {
        if (!$from instanceof MediaObject || !$to instanceof MediaObjectApi) {
            throw new \InvalidArgumentException('Unexpected types for mapping');
        }

        $path = $this->storage->resolveUri($from, 'file');

        if ($path) {
            $to->contentUrl = $path;
            $to->thumbUrl = $this->cacheManager->generateUrl(
                $path, 
                'avatar_min', 
                [], 
                null, 
                UrlGeneratorInterface::RELATIVE_PATH
            );
        }

        return $to;
    }


}
