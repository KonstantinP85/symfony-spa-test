<?php

namespace App\Entity;

use App\Repository\LinkRepository;
use App\Service\LinkStatus;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity(repositoryClass: LinkRepository::class)]
#[ORM\Table(name: 'app_link')]
class Link
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    private string $url;

    #[ORM\Column(type: 'string', length: 128)]
    private string $status;

    #[ORM\Column(type: 'integer')]
    private int $clickCount;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User $user;

    public function __construct(User $user, string $name, string $url)
    {
        $this->user = $user;
        $this->name = $name;
        $this->url = $url;
        $this->status = LinkStatus::DRAFT->value;
        $this->clickCount = 0;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getClickCount(): int
    {
        return $this->clickCount;
    }

    public function setClickCount(int $clickCount): void
    {
        $this->clickCount = $clickCount;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        if ($this->user === $user) {
            return;
        }

        $this->user = $user;
        $user->addLink($this);
    }

    public function addClick(): void
    {
        $this->clickCount = $this->clickCount + 1;
    }
}