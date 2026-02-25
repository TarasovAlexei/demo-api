<?php

namespace App\EventListener;

use App\Entity\MediaObject;
use App\Message\GenerateThumbnailMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class MediaUploadListener
{
    public function __construct(
        private MessageBusInterface $bus
    ) {}

    #[AsEventListener(event: Events::POST_UPLOAD)]
    public function onPostUpload(Event $event): void
    {
        $object = $event->getObject();

        if (!$object instanceof MediaObject) {
            return;
        }

        $fileName = $object->getFilePath();
        if (!$fileName) {
            return;
        }

        $path = 'media/avatars/' . $fileName;

        $this->bus->dispatch(new GenerateThumbnailMessage($path));
    }
}
