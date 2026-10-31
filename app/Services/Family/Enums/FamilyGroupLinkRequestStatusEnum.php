<?php

namespace App\Services\Family\Enums;

enum FamilyGroupLinkRequestStatusEnum: string
{
    case PENDING    = 'pending';
    case DONE       = 'done';
    case CANCELED   = 'canceled';
}
