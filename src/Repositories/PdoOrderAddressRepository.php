<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Repositories;

use DateTimeImmutable;
use PDO;
use Ttpryg\OrderEngine\Contracts\OrderAddressRepositoryInterface;
use Ttpryg\OrderEngine\Entities\OrderAddress;
use Ttpryg\OrderEngine\Enums\OrderAddressType;

class PdoOrderAddressRepository implements OrderAddressRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function save(OrderAddress $address): void
    {
        $existing = $this->findById($address->id);

        $sql = $existing instanceof \Ttpryg\OrderEngine\Entities\OrderAddress
            ? 'UPDATE order_addresses SET order_id = :order_id, type = :type, name = :name, phone = :phone, address = :address, city = :city, province = :province, postal_code = :postal_code, country = :country, updated_at = :updated_at WHERE id = :id'
            : 'INSERT INTO order_addresses (id, order_id, type, name, phone, address, city, province, postal_code, country, created_at, updated_at) VALUES (:id, :order_id, :type, :name, :phone, :address, :city, :province, :postal_code, :country, :created_at, :updated_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $address->id,
            'order_id' => $address->orderId,
            'type' => $address->type->value,
            'name' => $address->name,
            'phone' => $address->phone,
            'address' => $address->address,
            'city' => $address->city,
            'province' => $address->province,
            'postal_code' => $address->postalCode,
            'country' => $address->country,
            'created_at' => $address->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $address->updatedAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findById(string $id): ?OrderAddress
    {
        $stmt = $this->pdo->prepare('SELECT * FROM order_addresses WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToEntity($row) : null;
    }

    public function findByOrderId(string $orderId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM order_addresses WHERE order_id = :order_id');
        $stmt->execute(['order_id' => $orderId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map($this->mapToEntity(...), $rows);
    }

    public function findByOrderIdAndType(string $orderId, OrderAddressType|string $type): ?OrderAddress
    {
        $typeStr = is_string($type) ? $type : $type->value;
        $stmt = $this->pdo->prepare('SELECT * FROM order_addresses WHERE order_id = :order_id AND type = :type');
        $stmt->execute([
            'order_id' => $orderId,
            'type' => $typeStr,
        ]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToEntity($row) : null;
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM order_addresses WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    private function mapToEntity(array $row): OrderAddress
    {
        return new OrderAddress(
            id: (string) $row['id'],
            orderId: (string) $row['order_id'],
            type: (string) $row['type'],
            name: (string) $row['name'],
            phone: (string) $row['phone'],
            address: (string) $row['address'],
            city: (string) $row['city'],
            province: (string) $row['province'],
            postalCode: (string) $row['postal_code'],
            country: (string) $row['country'],
            createdAt: ! empty($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null,
            updatedAt: ! empty($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null
        );
    }
}
