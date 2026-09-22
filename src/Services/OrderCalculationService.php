<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Services;

use Ttpryg\OrderEngine\Entities\OrderItem;

class OrderCalculationService
{
    public function calculateItemSubtotal(
        int $quantity,
        float $unitPrice,
        float $discountTotal = 0.0,
        float $taxTotal = 0.0
    ): float {
        return max(0.0, ($quantity * $unitPrice) - $discountTotal + $taxTotal);
    }

    /**
     * @param  OrderItem[]  $items
     */
    public function calculateSubtotal(array $items): float
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += $item->calculateSubtotal();
        }

        return $subtotal;
    }

    public function calculateGrandTotal(
        float $subtotal,
        float $discountTotal = 0.0,
        float $taxTotal = 0.0,
        float $shippingTotal = 0.0
    ): float {
        return max(0.0, $subtotal - $discountTotal + $taxTotal + $shippingTotal);
    }
}
