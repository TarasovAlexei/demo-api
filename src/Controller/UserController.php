<?php

namespace App\Controller;

use App\ApiResource\UserApi;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(MicroMapperInterface $microMapper): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $userDto = $microMapper->map($user, UserApi::class);

        return $this->render('user/profile.html.twig', [
            'user_data' => $userDto,
        ]);
    }
}
