<?php

namespace App\Service;

enum LinkStatus: string
{
    case DRAFT = 'draft';

    case MODERATION = 'moderation';

    case PUBLISHED = 'published';
}