<?php

namespace App\Manager;

use App\Dto\Link\CreateLinkRequestDto;
use App\Dto\Link\LinkListRequestDto;
use App\Dto\Link\LinkResponseDto;
use App\Dto\PaginatedData;
use App\Entity\Link;
use App\Entity\User;
use App\Repository\LinkRepository;
use App\Service\DtoConverter;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class LinkManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DtoConverter $converter,
        private readonly LinkRepository $linkRepository,
        private readonly Security $security
    ){
    }

    public function list(LinkListRequestDto $linkListRequestDto): PaginatedData
    {
        $qb = $this->getQueryBuilder($linkListRequestDto);
             if (isset($linkListRequestDto->sort) && isset($linkListRequestDto->order)) {
                 $qb->addOrderBy('l.' . $linkListRequestDto->sort, $linkListRequestDto->order);
             }
        $items = $qb->getQuery()
            ->setMaxResults($linkListRequestDto->countPerPage)
            ->setFirstResult(($linkListRequestDto->page - 1) * $linkListRequestDto->countPerPage)
            ->getResult();


        $denormalizedItems = $this->converter->convertResponseToDto(
            $items,
            LinkResponseDto::class,
            true,
            $this->getCallBack()
        );

        $total = $this->getQueryBuilder($linkListRequestDto)
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
        ;

        return new PaginatedData($total, $linkListRequestDto->countPerPage, $linkListRequestDto->page, $denormalizedItems);
    }

    public function delete(int $id): void
    {
        $link = $this->getLinkFromRepo($id);

        $this->entityManager->remove($link);
        $this->entityManager->flush();
    }


    public function get(int $id): object
    {
        $link = $this->getLinkFromRepo($id);

        return $this->converter->convertResponseToDto(
            $link,
            LinkResponseDto::class,
            false,
            $this->getCallBack()
        );
    }

    public function create(CreateLinkRequestDto $createLinkRequestDto): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            //throw new \Exception('Auth error', Response::HTTP_FORBIDDEN);
        }

        $link = new Link(
            $this->entityManager->getRepository(User::class)->find(4),  // убрать
            $createLinkRequestDto->name,
            $createLinkRequestDto->url,
            2
        );

        $this->entityManager->persist($link);
        $this->entityManager->flush();
    }

    public function edit(CreateLinkRequestDto $createLinkRequestDto, $id): void
    {
        $link = $this->getLinkFromRepo($id);

        $link->setName($createLinkRequestDto->name);
        $link->setUrl($createLinkRequestDto->url);

        $this->entityManager->flush();
    }

    private function getLinkFromRepo(int $id): Link
    {
        $link = $this->linkRepository->find($id);

        if (!$link instanceof Link) {
            throw new \Exception('Link was not found', Response::HTTP_NOT_FOUND);
        }

        return $link;
    }

    private function getQueryBuilder(LinkListRequestDto $linkListRequestDto): QueryBuilder
    {
        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->from(Link::class, 'l')
            ->select(['l']);

        if (isset($linkListRequestDto->url)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('l.url', ':url'))
                ->setParameter('url', '%' . $linkListRequestDto->url . '%');
        }

        if (isset($linkListRequestDto->name)) {
            $queryBuilder->andWhere($queryBuilder->expr()->like('l.name', ':name'))
                ->setParameter('name',  '%' . $linkListRequestDto->name . '%');
        }

        if (isset($linkListRequestDto->clickCount)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('l.clickCount', ':clickCount'))
                ->setParameter('clickCount', $linkListRequestDto->clickCount);
        }

        return $queryBuilder;
    }

    private function getCallBack(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'user' =>  function ($innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
                    return $innerObject->getLogin();
                },
            ]
        ];
    }
}