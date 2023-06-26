<?php

namespace App\Dto\Link;

use Symfony\Component\Validator\Constraints as Assert;

class CreateLinkRequestDto
{
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Your link name cannot be shorter than 3 characters',
        maxMessage: 'Your link name cannot be longer than 255 characters',
    )]
    public string $name;

    #[Assert\Type('string')]
    #[Assert\Regex('/https?:\/\/(?:w{1,3}\.)?[^\s.]+(?:\.[a-z]+)*(?::\d+)?((?:\/\w+)|(?:-\w+))*\/?(?![^<]*(?:<\/\w+>|\/?>))/')]
    #[Assert\Length(
        min: 7,
        minMessage: 'It does not look like a link. Try to start with http...',
    )]
    public string $url;
}