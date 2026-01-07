<?php

namespace App\Normalizer;

use App\Entity\BlogPost;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

#[AsDecorator('api_platform.jsonld.normalizer.item')]
class AddAuthorGroupsNormalizer implements NormalizerInterface, SerializerAwareInterface
{
    public function __construct(private NormalizerInterface $normalizer, private Security $security)
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        if ($object instanceof BlogPost && $this->security->getUser() === $object->getAuthor()) {
            $context['groups'][] = 'author:read';
        }

        $normalized = $this->normalizer->normalize($object, $format, $context);

        if ($object instanceof BlogPost && $this->security->getUser() === $object->getAuthor()) {
            $normalized['isMine'] = true;
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $this->normalizer->supportsNormalization($data, $format);
    }

    public function setSerializer(SerializerInterface $serializer): void
    {
        if ($this->normalizer instanceof SerializerAwareInterface) {
            $this->normalizer->setSerializer($serializer);
        }
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            BlogPost::class => true,
        ];
    }
}