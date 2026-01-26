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
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    #[\Override]
    public static function class(): string
    {
        return MediaObject::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    #[\Override]
    protected function defaults(): array|callable
    {
        return [
            'file' => self::createFakeImage(),
        ];
    }

    private static function createFakeImage(): UploadedFile
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'fixture_avatar');
        $image = imagecreatetruecolor(200, 200);
        
        $color = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($image, 0, 0, $color);
        
        imagejpeg($image, $tempFile);
        imagedestroy($image);

        return new UploadedFile(
            $tempFile,
            'avatar_fixture.jpg',
            'image/jpeg',
            null,
            true
        );
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    #[\Override]
    protected function initialize(): static
    {
        return $this
            ->afterInstantiate(function(MediaObject $mediaObject): void {
                $mediaObject->setFile(null);
            })
        ;
    }
}
