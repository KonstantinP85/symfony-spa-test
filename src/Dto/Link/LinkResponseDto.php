<?php

namespace App\Dto\Link;

class LinkResponseDto
{
    public int $id;

    public string $name;

    public string $url;

    public int $status;

    public int $clickCount;

    public string $user;
}