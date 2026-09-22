<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Repositories;

use Ttpryg\OrderEngine\Contracts\OrderHistoryRepositoryInterface;
use Ttpryg\OrderEngine\Entities\OrderHistory;

class MemoryOrderHistoryRepository implements OrderHistoryRepositoryInterface
{
    /** @var array<string, OrderHistory> */
    private array $histories = [];

    public function save(OrderHistory $history): void
    {
        $this->histories[$history->id] = $history;
    }

    public function findByOrderId(string $orderId): array
    {
        $result = [];
        foreach ($this->histories as $history) {
            if ($history->orderId === $orderId) {
                $result[] = $history;
            }
        }

        return $result;
    }
}
