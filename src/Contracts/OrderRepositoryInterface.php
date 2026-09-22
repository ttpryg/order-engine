<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Contracts;

use Ttpryg\OrderEngine\Entities\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(string $id): ?Order;

    public function findByOrderNumber(string $orderNumber): ?Order;

    /**
     * @return Order[]
     */
    public function findByCustomer(string $customerType, string $customerId): array;

    public function delete(string $id): bool;
}
