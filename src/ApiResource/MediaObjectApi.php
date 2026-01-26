<?php

namespace App\ApiResource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use App\State\EntityClassDtoStateProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use App\Entity\MediaObject;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    shortName: 'MediaObject',
    operations: [
        new Get(),
        new Post(
            security: 'is_granted("ROLE_USER")',
            inputFormats: ['multipart' => ['multipart/form-data']],
            deserialize: false, 
            openapi: new \ApiPlatform\OpenApi\Model\Operation(
                requestBody: new \ApiPlatform\OpenApi\Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                    ],
                                ],
                            ],
                        ],
                    ])
                )
            )
        )
    ],
    processor: EntityClassDtoStateProcessor::class,
    stateOptions: new Options(entityClass: MediaObject::class),
)]
class MediaObjectApi
{
    #[ApiProperty(identifier: true, readable: true, writable: false)]
    public ?int $id = null;

    #[Assert\NotNull(message: 'The file is required to upload')]
    
    #[Assert\Image(
        maxSize: '2M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
        maxSizeMessage: 'The file is too large (maximum 2MB)',
        mimeTypesMessage: 'Accepted formats: JPG, PNG, WEBP'
    )]

    #[ApiProperty(readable: false, writable: true)]
    public mixed $file = null;

    #[ApiProperty(readable: true, writable: false)]
    public ?string $contentUrl = null;
}
