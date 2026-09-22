<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Contracts;

use Ttpryg\OrderEngine\Entities\OrderAddress;
use Ttpryg\OrderEngine\Enums\OrderAddressType;

interface OrderAddressRepositoryInterface
{
    public function save(OrderAddress $address): void;

    public function findById(string $id): ?OrderAddress;

    /**
     * @return OrderAddress[]
     */
    public function findByOrderId(string $orderId): array;

    public function findByOrderIdAndType(string $orderId, OrderAddressType|string $type): ?OrderAddress;

    public function delete(string $id): bool;
}
