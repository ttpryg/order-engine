<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Events;

use Ttpryg\OrderEngine\Entities\Order;

class OrderConfirmedEvent
{
    public function __construct(public readonly Order $order) {}
}
