<?php

namespace App\MessageHandler;

use App\Message\GenerateThumbnailMessage;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GenerateThumbnailHandler
{
    public function __construct(
        private CacheManager $cacheManager,
        private DataManager $dataManager,
        private FilterManager $filterManager
    ) {}

    public function __invoke(GenerateThumbnailMessage $message)
    {
        $path = $message->getPath();
        $filter = 'avatar_min';

        if ($this->cacheManager->isStored($path, $filter)) {
            return;
        }

        $binary = $this->dataManager->find($filter, $path);
        $content = $this->filterManager->applyFilter($binary, $filter);
        $this->cacheManager->store($content, $path, $filter);
    }
}
