<?php

namespace App\Dto\Link;

class LinkResponseDto
{
    public int $id;

    public string $name;

    public string $url;

    public string $status;

    public int $clickCount;

    public string $user;
}