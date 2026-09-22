<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Entities;

use DateTimeImmutable;
use Ttpryg\OrderEngine\Enums\OrderStatus;

class Order
{
    public OrderStatus $status;

    /** @var OrderItem[] */
    public array $items = [];

    /** @var OrderAddress[] */
    public array $addresses = [];

    public function __construct(
        public readonly string $id,
        public readonly string $orderNumber,
        public readonly string $customerType,
        public readonly string $customerId,
        OrderStatus|string $status = OrderStatus::PENDING,
        public string $currency = 'IDR',
        public float $subtotal = 0.0,
        public float $discountTotal = 0.0,
        public float $taxTotal = 0.0,
        public float $shippingTotal = 0.0,
        public float $grandTotal = 0.0,
        public ?string $note = null,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->status = is_string($status) ? OrderStatus::from($status) : $status;
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable;
    }

    public function addItem(OrderItem $item): void
    {
        $this->items[] = $item;
        $this->recalculateTotals();
        $this->updatedAt = new DateTimeImmutable;
    }

    public function removeItem(string $itemId): void
    {
        $this->items = array_values(array_filter(
            $this->items,
            fn (OrderItem $item) => $item->id !== $itemId
        ));
        $this->recalculateTotals();
        $this->updatedAt = new DateTimeImmutable;
    }

    public function updateItemQuantity(string $itemId, int $quantity): void
    {
        foreach ($this->items as $item) {
            if ($item->id === $itemId) {
                $item->quantity = max(1, $quantity);
                $item->calculateSubtotal();
                break;
            }
        }
        $this->recalculateTotals();
        $this->updatedAt = new DateTimeImmutable;
    }

    public function recalculateTotals(): void
    {
        $subtotal = 0.0;
        foreach ($this->items as $item) {
            $subtotal += $item->calculateSubtotal();
        }
        $this->subtotal = $subtotal;
        $this->grandTotal = max(0.0, $this->subtotal - $this->discountTotal + $this->taxTotal + $this->shippingTotal);
    }
}
