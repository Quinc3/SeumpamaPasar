<?php

namespace App\Enums;

enum DigitalServiceType: string
{
    case ElectricityBill = 'electricity_bill';
    case ElectricityToken = 'electricity_token';
    case Pulse = 'pulse';
    case DataPackage = 'data_package';
}