<?php

namespace App\Controller;

use ApiPlatform\Metadata\IriConverterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;


final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(IriConverterInterface $iriConverter, #[CurrentUser] $user = null): Response
    {
        if (!$user) {
            return $this->json([
                'error' => 'Invalid login request',
            ], 401);
        }
        return new Response(null, 204, [
            'Location' => $iriConverter->getIriFromResource($user),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }

}
