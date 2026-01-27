<?php

namespace App\Factory;

use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<MediaObject>
 */
final class MediaObjectFactory extends PersistentProxyObjectFactory
{
    protected function defaults(): array|callable
    {
        return [
            'file' => self::createFakeImage(),
        ];
    }

    private static function createFakeImage(): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'avatar');
        $image = imagecreatetruecolor(200, 200);
        
        $color = imagecolorallocate($image, rand(100, 255), rand(100, 255), rand(100, 255));
        imagefill($image, 0, 0, $color);
        
        imagejpeg($image, $tempFile);
        imagedestroy($image);

        return new UploadedFile(
            $tempFile,
            uniqid('avatar_') . '.jpg', 
            'image/jpeg',
            null,
            true 
        );
    }

    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(MediaObject $mediaObject): void {
                $mediaObject->setFile(null);
            })
        ;
    }

    public static function class(): string
    {
        return MediaObject::class;
    }
}
