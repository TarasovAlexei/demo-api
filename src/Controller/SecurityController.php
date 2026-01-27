<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(Request $request): Response
    {
        $user = $this->getUser();
        
        if ($user && $request->isMethod('GET')) {
            return $this->redirectToRoute('app_user', ['id' => $user->getId()]);
        }

        if ($request->isMethod('POST')) {
            return $this->json([
                'redirect_url' => $this->generateUrl('app_user', ['id' => $user->getId()])
            ]);
        }

        return $this->render('security/index.html.twig');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \Exception('This should never be reached!');
    }
}
