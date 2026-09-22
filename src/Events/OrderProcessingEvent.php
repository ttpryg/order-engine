<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Events;

use Ttpryg\OrderEngine\Entities\Order;

class OrderProcessingEvent
{
    public function __construct(public readonly Order $order) {}
}
