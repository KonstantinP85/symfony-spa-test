<?php

namespace App\Service;

enum LinkStatus: int
{
    case DRAFT = 1;

    case MODERATION = 2;

    case PUBLISHED = 3;
}