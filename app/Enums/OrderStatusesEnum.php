<?php

namespace App\Enums;

enum OrderStatusesEnum: string
{
    case CREATED = 'Created';
    case COMPLETED = 'Completed';
    case DELETED = 'Deleted';
}
