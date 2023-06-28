<?php

namespace App\Manager;

use App\Dto\Link\CreateLinkRequestDto;
use App\Dto\Link\LinkListRequestDto;
use App\Dto\Link\LinkResponseDto;
use App\Dto\PaginatedData;
use App\Entity\Link;
use App\Entity\User;
use App\Exception\BadParamsException;
use App\Repository\LinkRepository;
use App\Service\DtoConverter;
use App\Service\LinkStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Workflow\WorkflowInterface;

class LinkManager
{
    private const ACTION_FOR_LINK = 'click';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly DtoConverter $converter,
        private readonly LinkRepository $linkRepository,
        private readonly Security $security,
        private readonly WorkflowInterface $linkStatusStateMachine,
        private readonly string $linkHost
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
            $this->getContext()
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
            $this->getContext()
        );
    }

    public function create(CreateLinkRequestDto $createLinkRequestDto): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \Exception('Auth error', Response::HTTP_FORBIDDEN);
        }

        $existedLinkWithSameUrl = $this->getLinkByUrl($createLinkRequestDto->url);

        if ($existedLinkWithSameUrl instanceof Link) {
            throw new BadParamsException(['url' => 'Link with this url has already been created']);
        }

        $link = new Link(
            $user,
            $createLinkRequestDto->name,
            $createLinkRequestDto->url
        );

        try {
            $this->linkStatusStateMachine->apply($link, 'to_moderation');
        } catch (\LogicException $e) {
            throw new \Exception('Status state machine error', Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($link);
        $this->entityManager->flush();
    }

    public function edit(CreateLinkRequestDto $createLinkRequestDto, $id): void
    {
        $link = $this->getLinkFromRepo($id);

        $existedLinkWithSameUrl = $this->getLinkByUrl($createLinkRequestDto->url);

        if ($existedLinkWithSameUrl instanceof Link && $existedLinkWithSameUrl->getId() !== $id) {
            throw new BadParamsException(['url' => 'Link with this url has already been created']);
        }

        $link->setName($createLinkRequestDto->name);
        $link->setUrl($createLinkRequestDto->url);

        $this->entityManager->flush();
    }

    public function moderation(int $id, string $status): void
    {
        $link = $this->getLinkFromRepo($id);

        $fn = function($transition) use ($link) {
            $this->linkStatusStateMachine->apply($link, $transition);
        };

        try {
            match ($status) {
                LinkStatus::DRAFT->value => $fn('to_draft'),
                LinkStatus::MODERATION->value => $fn('to_moderation'),
                LinkStatus::PUBLISHED->value => $fn('moderated'),
            };
        } catch (\LogicException $e) {
            throw new \Exception('Status s1tate machine error', Response::HTTP_BAD_REQUEST);
        }

        $link->setStatus($status);

        $this->entityManager->flush();
    }

    public function click($identifier): ?int
    {
        $link = $this->linkRepository->findOneBy(['url' => $this->buildLink($identifier)]);

        if (!$link instanceof Link) {
            return null;
        }
        $link->addClick();
        $this->entityManager->flush();

        return $link->getId();
    }

    private function getLinkByUrl(string $url): ?Link
    {
        $link = $this->linkRepository->findOneBy(['url' => $url]);

        return $link;
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

    private function buildLink(string $identifier)
    {
        return sprintf('%s%s/%s', $this->linkHost, self::ACTION_FOR_LINK, $identifier);
    }

    private function getContext(): array
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