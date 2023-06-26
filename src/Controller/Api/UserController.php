<?php

namespace App\Controller\Api;

use App\Dto\User\EditUserRequestDto;
use App\Dto\User\UserListRequestDto;
use App\Dto\User\UserResponseDto;
use App\Entity\User;
use App\Manager\UserManager;
use App\Service\DtoConverter;
use App\Service\ErrorResponse;
use App\Service\UserChangeRoles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[Route('/api/v1/user')]
class UserController extends AbstractController
{
    #[Route('/list', name: 'api.v1.user.list', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the list.')]
    public function list(Request $request, DtoConverter $converter, UserManager $manager): JsonResponse
    {
        try {
            $dto = $converter->convertRequestToDto(UserListRequestDto::class, $request);
            $userList = $manager->list($dto);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse($userList, Response::HTTP_OK);
    }

    #[Route('', name: 'api.v1.user.current', methods: ['GET'])]
    #[IsGranted('ROLE_USER', message: 'You are not allowed to access the info.')]
    public function currentUser(Request $request, DtoConverter $converter): JsonResponse
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new \Exception('Auth error', Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse($converter->convertResponseToDto($user, UserResponseDto::class, false, [AbstractNormalizer::ATTRIBUTES => ['id', 'login', 'roles']]), Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api.v1.user.get', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the entity.')]
    public function get(Request $request, UserManager $manager, int $id): JsonResponse
    {
        try {
            $user = $manager->get($id);
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new JsonResponse($user, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api.v1.user.edit', methods: ['PUT'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the action.')]
    public function edit(Request $request, DtoConverter $converter, UserManager $manager, int $id): JsonResponse
    {
        try {
            $dto = $converter->convertRequestToDto(EditUserRequestDto::class, $request);
            $manager->edit($dto, $id);
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api.v1.user.delete', methods: ['DELETE'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the action.')]
    public function delete(Request $request, UserManager $manager, int $id): JsonResponse
    {
        try {
            $manager->delete($id);
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api.v1.user.roles', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the action.')]
    public function moderate(Request $request, UserManager $manager, int $id): JsonResponse
    {
        $action = $request->query->get('action');
        if ($action !== UserChangeRoles::MAKE_USER->value && $action !== UserChangeRoles::MAKE_ADMIN->value) {
            return new ErrorResponse('Wrong action');
        }

        try {
            $manager->changeRoles($id, $action);
        } catch (\Exception $e) {
            return new ErrorResponse($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}