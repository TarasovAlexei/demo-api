<?php

namespace App\Tests\Functional;

use App\Entity\MediaObject;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\ResetDatabase;

class MediaObjectResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testUploadAvatar(): void
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), 'test_image');
        $gdImage = imagecreatetruecolor(10, 10);
        imagejpeg($gdImage, $tempFilePath);
        imagedestroy($gdImage);

        $file = new UploadedFile($tempFilePath, 'test_avatar.jpg', 'image/jpeg', null, true);

        $this->browser()
            ->post('/api/media_objects', [
                'files' => ['file' => $file],
            ])
            ->assertStatus(201)
            ->use(function (Json $json) {
                $json->assertHas('id');
                $json->assertHas('contentUrl');
                
                $url = $json->search('contentUrl');
                self::assertStringContainsString('/media/avatars/', $url);
            })
        ;

        $container = self::getContainer();
        /** @var MediaObject $mediaObject */
        $mediaObject = $container->get('doctrine')
            ->getRepository(MediaObject::class)
            ->findOneBy([], ['id' => 'DESC']);

        self::assertNotNull($mediaObject);
        
        $publicDir = $container->getParameter('kernel.project_dir') . '/public';
        self::assertFileExists($publicDir . '/media/avatars/' . $mediaObject->getFilePath());

        if (file_exists($tempFilePath)) {
            @unlink($tempFilePath);
        }
    }
}
