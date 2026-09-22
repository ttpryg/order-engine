<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Events;

use Ttpryg\OrderEngine\Entities\Order;

class OrderCancelledEvent
{
    public function __construct(
        public readonly Order $order,
        public readonly ?string $reason = null
    ) {}
}
