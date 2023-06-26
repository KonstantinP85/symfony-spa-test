<?php

namespace App\Dto\User;

use Symfony\Component\Validator\Constraints as Assert;

class EditUserRequestDto
{
    #[Assert\Type('string')]
    #[Assert\Length(
        min: 3,
        max: 255,
        minMessage: 'Your login cannot be shorter than 3 characters',
        maxMessage: 'Your link name cannot be longer than 255 characters',
    )]
    public string $login;
}