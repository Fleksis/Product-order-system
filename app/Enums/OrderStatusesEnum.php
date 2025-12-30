<?php

namespace App\Enums;

enum OrderStatusesEnum: string
{
    case CREATED = 'Created';
    case COMPLETED = 'Completed';
    case CANCELED = 'Canceled';
    case DELETED = 'Deleted';
}
