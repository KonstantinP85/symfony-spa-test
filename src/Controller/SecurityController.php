<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route(path: '/api/security/login', name: 'api_security_login', methods: ['POST'])]
    public function login(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, 'Invalid credentials');
        }

        return $this->json([
            'login' => $user->getLogin(),
            'roles' => $user->getRoles(),
        ]);
    }

    #[Route(path: '/api/security/logout', name: 'app_security_logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \Exception('logout exception');
    }
}