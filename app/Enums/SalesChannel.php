<?php

namespace App\Enums;

enum SalesChannel: string
{
    case B2C = 'b2c';
    case B2B = 'b2b';
    case Both = 'both';
}