<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $user = $this->getUser();

        if ($user) {
            return $this->redirectToRoute('app_user', ['id' => $user->getId()]);
        }

        return $this->redirectToRoute('app_login');
    }

}
