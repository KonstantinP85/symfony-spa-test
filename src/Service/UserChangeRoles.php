<?php

namespace App\Service;

enum UserChangeRoles: string
{
    case MAKE_ADMIN = 'make_admin';

    case MAKE_USER = 'make_user';
}