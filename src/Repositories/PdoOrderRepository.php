<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Repositories;

use DateTimeImmutable;
use PDO;
use Ttpryg\OrderEngine\Contracts\OrderRepositoryInterface;
use Ttpryg\OrderEngine\Entities\Order;
use Ttpryg\OrderEngine\Enums\OrderStatus;

class PdoOrderRepository implements OrderRepositoryInterface
{
    public function __construct(private readonly PDO $pdo) {}

    public function save(Order $order): void
    {
        $existing = $this->findById($order->id);

        $sql = $existing instanceof \Ttpryg\OrderEngine\Entities\Order
            ? 'UPDATE orders SET order_number = :order_number, customer_type = :customer_type, customer_id = :customer_id, status = :status, currency = :currency, subtotal = :subtotal, discount_total = :discount_total, tax_total = :tax_total, shipping_total = :shipping_total, grand_total = :grand_total, note = :note, updated_at = :updated_at WHERE id = :id'
            : 'INSERT INTO orders (id, order_number, customer_type, customer_id, status, currency, subtotal, discount_total, tax_total, shipping_total, grand_total, note, created_at, updated_at) VALUES (:id, :order_number, :customer_type, :customer_id, :status, :currency, :subtotal, :discount_total, :tax_total, :shipping_total, :grand_total, :note, :created_at, :updated_at)';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'id' => $order->id,
            'order_number' => $order->orderNumber,
            'customer_type' => $order->customerType,
            'customer_id' => $order->customerId,
            'status' => $order->status->value,
            'currency' => $order->currency,
            'subtotal' => $order->subtotal,
            'discount_total' => $order->discountTotal,
            'tax_total' => $order->taxTotal,
            'shipping_total' => $order->shippingTotal,
            'grand_total' => $order->grandTotal,
            'note' => $order->note,
            'created_at' => $order->createdAt->format('Y-m-d H:i:s'),
            'updated_at' => $order->updatedAt->format('Y-m-d H:i:s'),
        ]);
    }

    public function findById(string $id): ?Order
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToEntity($row) : null;
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE order_number = :order_number');
        $stmt->execute(['order_number' => $orderNumber]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToEntity($row) : null;
    }

    public function findByCustomer(string $customerType, string $customerId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM orders WHERE customer_type = :customer_type AND customer_id = :customer_id ORDER BY created_at DESC');
        $stmt->execute([
            'customer_type' => $customerType,
            'customer_id' => $customerId,
        ]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map($this->mapToEntity(...), $rows);
    }

    public function delete(string $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM orders WHERE id = :id');

        return $stmt->execute(['id' => $id]);
    }

    private function mapToEntity(array $row): Order
    {
        return new Order(
            id: (string) $row['id'],
            orderNumber: (string) $row['order_number'],
            customerType: (string) $row['customer_type'],
            customerId: (string) $row['customer_id'],
            status: OrderStatus::from((string) $row['status']),
            currency: (string) $row['currency'],
            subtotal: (float) $row['subtotal'],
            discountTotal: (float) $row['discount_total'],
            taxTotal: (float) $row['tax_total'],
            shippingTotal: (float) $row['shipping_total'],
            grandTotal: (float) $row['grand_total'],
            note: $row['note'] ?? null,
            createdAt: ! empty($row['created_at']) ? new DateTimeImmutable($row['created_at']) : null,
            updatedAt: ! empty($row['updated_at']) ? new DateTimeImmutable($row['updated_at']) : null
        );
    }
}
