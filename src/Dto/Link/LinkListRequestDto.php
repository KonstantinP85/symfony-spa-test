<?php

namespace App\Dto\Link;

use App\Dto\ListRequestDto;
use Symfony\Component\Validator\Constraints as Assert;

class LinkListRequestDto extends ListRequestDto
{
    #[Assert\Choice(['id', 'name', 'status', 'clickCount'])]
    public string $sort;

    #[Assert\Type('string')]
    public ?string $name;

    #[Assert\Type('string')]
    public ?string $url;

    #[Assert\PositiveOrZero]
    public ?int $clickCount;
}