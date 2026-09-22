<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\OrderEngine\Entities\OrderItem;
use Ttpryg\OrderEngine\Services\OrderCalculationService;

class OrderCalculationServiceTest extends TestCase
{
    private OrderCalculationService $calculator;

    protected function setUp(): void
    {
        $this->calculator = new OrderCalculationService;
    }

    public function test_calculate_item_subtotal(): void
    {
        $subtotal = $this->calculator->calculateItemSubtotal(
            quantity: 2,
            unitPrice: 50000.0,
            discountTotal: 10000.0,
            taxTotal: 5000.0
        );

        $this->assertEquals(95000.0, $subtotal);
    }

    public function test_calculate_subtotal_and_grand_total(): void
    {
        $item1 = new OrderItem(
            id: 'item-1',
            orderId: 'ord-1',
            productId: 'prod-1',
            name: 'Product A',
            quantity: 2,
            unitPrice: 100000.0
        );

        $item2 = new OrderItem(
            id: 'item-2',
            orderId: 'ord-1',
            productId: 'prod-2',
            name: 'Product B',
            quantity: 1,
            unitPrice: 50000.0
        );

        $subtotal = $this->calculator->calculateSubtotal([$item1, $item2]);
        $this->assertEquals(250000.0, $subtotal);

        $grandTotal = $this->calculator->calculateGrandTotal(
            subtotal: $subtotal,
            discountTotal: 20000.0,
            taxTotal: 10000.0,
            shippingTotal: 15000.0
        );

        $this->assertEquals(255000.0, $grandTotal);
    }
}
