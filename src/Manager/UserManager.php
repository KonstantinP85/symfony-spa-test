<?php

namespace App\Manager;

use App\Dto\Link\CreateLinkRequestDto;
use App\Dto\Link\LinkResponseDto;
use App\Dto\PaginatedData;
use App\Dto\User\EditUserRequestDto;
use App\Dto\User\UserListRequestDto;
use App\Dto\User\UserResponseDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\DtoConverter;
use App\Service\UserChangeRoles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class UserManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DtoConverter $converter,
        private readonly UserRepository $userRepository,
        private readonly Security $security
    ){
    }

    public function list(UserListRequestDto $userListRequestDto): PaginatedData
    {
        $qb = $this->getQueryBuilder($userListRequestDto);
        if (isset($userListRequestDto->sort) && isset($userListRequestDto->order)) {
            $qb->addOrderBy('u.' . $userListRequestDto->sort, $userListRequestDto->order);
        }
        $items = $qb->getQuery()
            ->setMaxResults($userListRequestDto->countPerPage)
            ->setFirstResult(($userListRequestDto->page - 1) * $userListRequestDto->countPerPage)
            ->getResult();


        $denormalizedItems = $this->converter->convertResponseToDto(
            $items,
            UserResponseDto::class,
            true,
            $this->getContext()
        );

        $total = $this->getQueryBuilder($userListRequestDto)
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
        ;

        return new PaginatedData($total, $userListRequestDto->countPerPage, $userListRequestDto->page, $denormalizedItems);
    }

    private function getQueryBuilder(UserListRequestDto $userListRequestDto): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->from(User::class, 'u')
            ->select(['u']);

        if (isset($userListRequestDto->login)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('u.login', ':login'))
                ->setParameter('login', '%' . $userListRequestDto->login . '%');
        }

        return $queryBuilder;
    }

    public function get(int $id): object
    {
        $user = $this->getUserFromRepo($id);

        return $this->converter->convertResponseToDto(
            $user,
            UserResponseDto::class,
            false,
            $this->getContext()
        );
    }

    public function edit(EditUserRequestDto $editUserRequestDto, $id): void
    {
        $link = $this->getUserFromRepo($id);

        $link->setLogin($editUserRequestDto->login);

        $this->entityManager->flush();
    }

    public function changeRoles(int $id, $action): void
    {
        $user = $this->getUserFromRepo($id);

        if ($action === UserChangeRoles::MAKE_USER->value) {
            $user->setRoles(['ROLE_USER']);
        }
        if ($action === UserChangeRoles::MAKE_ADMIN->value) {
            $user->setRoles(['ROLE_ADMIN']);
        }

        $this->entityManager->flush();
    }

    public function delete(int $id): void
    {
        $user = $this->getUserFromRepo($id);

        $this->entityManager->remove($user);
        $this->entityManager->flush();
    }

    private function getUserFromRepo(int $id): User
    {
        $user = $this->userRepository->find($id);

        if (!$user instanceof User) {
            throw new \Exception('User was not found', Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    private function getContext(): array
    {
        return [AbstractNormalizer::ATTRIBUTES => ['id', 'login', 'roles']];
    }
}