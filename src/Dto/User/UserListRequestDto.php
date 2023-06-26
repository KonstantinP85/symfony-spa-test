<?php

namespace App\Dto\User;

use App\Dto\ListRequestDto;
use Symfony\Component\Validator\Constraints as Assert;

class UserListRequestDto extends ListRequestDto
{
    #[Assert\Choice(['id', 'login'])]
    public string $sort;

    #[Assert\Type('string')]
    public ?string $login;
}