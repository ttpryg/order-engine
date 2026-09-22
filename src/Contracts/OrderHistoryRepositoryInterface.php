<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Contracts;

use Ttpryg\OrderEngine\Entities\OrderHistory;

interface OrderHistoryRepositoryInterface
{
    public function save(OrderHistory $history): void;

    /**
     * @return OrderHistory[]
     */
    public function findByOrderId(string $orderId): array;
}
