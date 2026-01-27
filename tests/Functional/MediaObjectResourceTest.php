<?php

namespace App\Tests\Functional;

use App\Entity\MediaObject;
use App\Factory\UserFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Zenstruck\Browser\Json;
use Zenstruck\Foundry\Test\ResetDatabase;

class MediaObjectResourceTest extends ApiTestCase
{
    use ResetDatabase;

    public function testUploadAndSetUserAvatar(): void
    {
        $user = UserFactory::createOne([
            'password' => 'pass',
            'roles' => ['ROLE_USER', 'ROLE_USER_EDIT']
        ]);

        $tempFilePath = tempnam(sys_get_temp_dir(), 'avatar');
        $gdImage = imagecreatetruecolor(10, 10);
        imagejpeg($gdImage, $tempFilePath);
        imagedestroy($gdImage);

        $file = new UploadedFile($tempFilePath, 'avatar.jpg', 'image/jpeg', null, true);

        $avatarIri = '';
        $this->browser()
            ->actingAs($user)
            ->post('/api/media_objects', [
                'files' => ['file' => $file],
            ])
            ->assertStatus(201)
            ->use(function (Json $json) use (&$avatarIri) {
                $avatarIri = $json->search('"@id"');
            })
        ;

        $this->browser()
            ->actingAs($user)
            ->patch('/api/users/'.$user->getId(), [
                'json' => [
                    'avatar' => $avatarIri,
                ],
                'headers' => ['Content-Type' => 'application/merge-patch+json']
            ])
            ->assertStatus(200)
            ->use(function (Json $json) {
                $actualContentUrl = $json->search('avatar.contentUrl');
                self::assertMatchesRegularExpression(
                    '/^\/media\/avatars\/.+/', 
                    $actualContentUrl
                );
                
                $json->assertHas('avatar.id');
            })
        ;
        
        @unlink($tempFilePath);
    }
}
