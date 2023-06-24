<?php

namespace App\Controller\Api;

use App\Dto\Link\CreateLinkRequestDto;
use App\Dto\Link\LinkListRequestDto;
use App\Manager\LinkManager;
use App\Service\DtoConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/link')]
class LinkController extends AbstractController
{
    #[Route('/list', name: 'api.v1.link.list', methods: ['GET'])]
    //#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the list.')]
    public function list(Request $request, DtoConverter $converter, LinkManager $manager): JsonResponse
    {
        try {
            $dto = $converter->convertRequestToDto(LinkListRequestDto::class, $request);
            $linkList = $manager->list($dto);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse($linkList, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'api.v1.link.get', methods: ['GET'])]
    //#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the list.')]
    public function get(Request $request, LinkManager $manager, int $id): JsonResponse
    {
        try {
            $link = $manager->get($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse($link, Response::HTTP_OK);
    }

    #[Route('/create', name: 'api.v1.link.create', methods: ['POST'])]
    public function create(Request $request, DtoConverter $converter, LinkManager $manager): JsonResponse
    {
        try {
            $dto = $converter->convertRequestToDto(CreateLinkRequestDto::class, $request);
            $manager->create($dto);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api.v1.link.edit', methods: ['PUT'])]
    //#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the list.')]
    public function edit(Request $request, DtoConverter $converter, LinkManager $manager, int $id): JsonResponse
    {
        try {
            $dto = $converter->convertRequestToDto(CreateLinkRequestDto::class, $request);
            $manager->edit($dto, $id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route('/{id}', name: 'api.v1.link.delete', methods: ['DELETE'])]
    //#[IsGranted('ROLE_ADMIN', message: 'You are not allowed to access the list.')]
    public function delete(Request $request, LinkManager $manager, int $id): JsonResponse
    {
        try {
            $manager->delete($id);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}