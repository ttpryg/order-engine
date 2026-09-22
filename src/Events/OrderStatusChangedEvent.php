<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Events;

use Ttpryg\OrderEngine\Entities\Order;
use Ttpryg\OrderEngine\Enums\OrderStatus;

class OrderStatusChangedEvent
{
    public function __construct(
        public readonly Order $order,
        public readonly OrderStatus $fromStatus,
        public readonly OrderStatus $toStatus
    ) {}
}
