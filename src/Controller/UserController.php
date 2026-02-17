<?php

namespace App\Controller;

use App\ApiResource\UserApi;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfonycasts\MicroMapper\MicroMapperInterface;

final class UserController extends AbstractController
{
    #[Route('/user/{id}', name: 'app_user')]
    public function index(
        int $id,
        UserRepository $userRepository, 
        MicroMapperInterface $microMapper
    ): Response {

        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $requestedUser = $userRepository->findUserForProfile($id);

        if (!$requestedUser) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('user/profile.html.twig', [
            'user_data' => $microMapper->map($requestedUser, UserApi::class),
        ]);
    }
}
