<?php

namespace App\Enum;

enum OrderStatus: string
{
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
