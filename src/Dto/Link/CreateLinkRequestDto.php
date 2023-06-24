<?php

namespace App\Dto\Link;

use Symfony\Component\Validator\Constraints as Assert;

class CreateLinkRequestDto
{
    #[Assert\Type('string')]
    public string $name;

    #[Assert\Type('string')]
    public string $url;
}