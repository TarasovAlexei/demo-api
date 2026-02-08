<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\BlogPostApi;
use App\Message\PostPublishedNotification;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class BlogPostStateProcessor implements ProcessorInterface
{
    public function __construct(
        private EntityClassDtoStateProcessor $innerProcessor,
        private MessageBusInterface $bus,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        $result = $this->innerProcessor->process($data, $operation, $uriVariables, $context);

        $previousData = $context['previous_data'] ?? null;

        if ($previousData instanceof BlogPostApi
            && $data->isPublished
            && $previousData->isPublished !== $data->isPublished
        ) {
            $this->bus->dispatch(new PostPublishedNotification($data->id));
        }

        return $result;
    }
}
