<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ListRequestDto
{
    #[Assert\Positive]
    public int $page;

    #[Assert\Positive]
    public int $countPerPage;


    #[Assert\Choice(['asc', 'desc'])]
    public string $order;
}