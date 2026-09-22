<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Entities;

use DateTimeImmutable;

class OrderItem
{
    public function __construct(
        public readonly string $id,
        public readonly string $orderId,
        public readonly string $productId,
        public ?string $variantId = null,
        public ?string $sku = null,
        public string $name = '',
        public int $quantity = 1,
        public float $unitPrice = 0.0,
        public float $discountTotal = 0.0,
        public float $taxTotal = 0.0,
        public float $subtotal = 0.0,
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable;
        if ($this->subtotal === 0.0 && $this->quantity > 0 && $this->unitPrice > 0.0) {
            $this->subtotal = ($this->quantity * $this->unitPrice) - $this->discountTotal + $this->taxTotal;
        }
    }

    public function calculateSubtotal(): float
    {
        $this->subtotal = ($this->quantity * $this->unitPrice) - $this->discountTotal + $this->taxTotal;

        return $this->subtotal;
    }
}
