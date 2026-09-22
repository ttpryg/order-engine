<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Repositories;

use Ttpryg\OrderEngine\Contracts\OrderAddressRepositoryInterface;
use Ttpryg\OrderEngine\Entities\OrderAddress;
use Ttpryg\OrderEngine\Enums\OrderAddressType;

class MemoryOrderAddressRepository implements OrderAddressRepositoryInterface
{
    /** @var array<string, OrderAddress> */
    private array $addresses = [];

    public function save(OrderAddress $address): void
    {
        $this->addresses[$address->id] = $address;
    }

    public function findById(string $id): ?OrderAddress
    {
        return $this->addresses[$id] ?? null;
    }

    public function findByOrderId(string $orderId): array
    {
        $result = [];
        foreach ($this->addresses as $address) {
            if ($address->orderId === $orderId) {
                $result[] = $address;
            }
        }

        return $result;
    }

    public function findByOrderIdAndType(string $orderId, OrderAddressType|string $type): ?OrderAddress
    {
        $typeEnum = is_string($type) ? OrderAddressType::from($type) : $type;
        foreach ($this->addresses as $address) {
            if ($address->orderId === $orderId && $address->type === $typeEnum) {
                return $address;
            }
        }

        return null;
    }

    public function delete(string $id): bool
    {
        if (isset($this->addresses[$id])) {
            unset($this->addresses[$id]);

            return true;
        }

        return false;
    }
}
