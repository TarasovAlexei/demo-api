<?php

namespace App\Mapper;

use App\ApiResource\MediaObjectApi;
use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfonycasts\MicroMapper\AsMapper;
use Symfonycasts\MicroMapper\MapperInterface;

#[AsMapper(from: MediaObjectApi::class, to: MediaObject::class)]
class MediaObjectApiToEntityMapper implements MapperInterface
{
    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function load(object $from, string $toClass, array $context): object
    {
        if (!$from instanceof MediaObjectApi) {
            throw new \InvalidArgumentException('Expected MediaObjectApi');
        }

        return new MediaObject();
    }

    public function populate(object $from, object $to, array $context): object
    {
        if (!$from instanceof MediaObjectApi || !$to instanceof MediaObject) {
            throw new \InvalidArgumentException('Unexpected types for mapping');
        }

        $file = $from->file;

        if (!$file) {
            $request = $this->requestStack->getCurrentRequest();
            if ($request) {
                $file = $request->files->get('file');
            }
        }

        if ($file) {
            $to->setFile($file);
        }

        return $to;
    }
}
