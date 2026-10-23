<?php

namespace App\Enums;

enum DigitalTransactionStatus: string
{
    case Pending = 'pending';
    case Success = 'success';
    case Failed = 'failed';
}