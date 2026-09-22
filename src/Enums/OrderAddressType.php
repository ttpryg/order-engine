<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Enums;

enum OrderAddressType: string
{
    case BILLING = 'billing';
    case SHIPPING = 'shipping';
}
