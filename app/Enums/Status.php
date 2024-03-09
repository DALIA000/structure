<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case ARCHIVED = 'archived';
    case REJECTED = 'rejected';
    case BLOCKED = 'blocked';
    case NEEDACTION = 'needaction';
}
