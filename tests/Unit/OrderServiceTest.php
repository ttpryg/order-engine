<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\OrderEngine\Entities\OrderAddress;
use Ttpryg\OrderEngine\Entities\OrderItem;
use Ttpryg\OrderEngine\Enums\OrderAddressType;
use Ttpryg\OrderEngine\Enums\OrderStatus;
use Ttpryg\OrderEngine\Repositories\MemoryOrderAddressRepository;
use Ttpryg\OrderEngine\Repositories\MemoryOrderHistoryRepository;
use Ttpryg\OrderEngine\Repositories\MemoryOrderItemRepository;
use Ttpryg\OrderEngine\Repositories\MemoryOrderRepository;
use Ttpryg\OrderEngine\Services\OrderService;

class OrderServiceTest extends TestCase
{
    private MemoryOrderRepository $orderRepo;

    private MemoryOrderItemRepository $itemRepo;

    private MemoryOrderAddressRepository $addressRepo;

    private MemoryOrderHistoryRepository $historyRepo;

    private OrderService $orderService;

    protected function setUp(): void
    {
        $this->orderRepo = new MemoryOrderRepository;
        $this->itemRepo = new MemoryOrderItemRepository;
        $this->addressRepo = new MemoryOrderAddressRepository;
        $this->historyRepo = new MemoryOrderHistoryRepository;

        $this->orderService = new OrderService(
            $this->orderRepo,
            $this->itemRepo,
            $this->addressRepo,
            $this->historyRepo
        );
    }

    public function test_create_order_with_product_snapshot_and_addresses(): void
    {
        $item = new OrderItem(
            id: 'item-101',
            orderId: 'order-101',
            productId: 'prod-500',
            sku: 'SKU-500',
            name: 'Original Product Name',
            quantity: 2,
            unitPrice: 150000.0
        );

        $billingAddress = new OrderAddress(
            id: 'addr-1',
            orderId: 'order-101',
            type: OrderAddressType::BILLING,
            name: 'Budi Santoso',
            phone: '08123456789',
            address: 'Jl. Merdeka No. 10',
            city: 'Jakarta Pusat',
            province: 'DKI Jakarta',
            postalCode: '10110'
        );

        $order = $this->orderService->createOrder(
            id: 'order-101',
            customerType: 'user',
            customerId: 'user-777',
            items: [$item],
            addresses: [$billingAddress],
            orderNumber: 'ORD-2026-0001',
            shippingTotal: 20000.0
        );

        $this->assertEquals('ORD-2026-0001', $order->orderNumber);
        $this->assertEquals(OrderStatus::PENDING, $order->status);
        $this->assertEquals(300000.0, $order->subtotal);
        $this->assertEquals(320000.0, $order->grandTotal);

        // Verify product snapshot integrity
        $this->assertEquals('Original Product Name', $order->items[0]->name);
        $this->assertEquals(150000.0, $order->items[0]->unitPrice);
    }

    public function test_add_remove_and_update_items(): void
    {
        $this->orderService->createOrder(
            id: 'order-102',
            customerType: 'member',
            customerId: 'M-123'
        );

        $item1 = new OrderItem(
            id: 'item-201',
            orderId: 'order-102',
            productId: 'prod-1',
            name: 'Item A',
            quantity: 1,
            unitPrice: 50000.0
        );

        $this->orderService->addItem('order-102', $item1);
        $fetched = $this->orderService->getOrder('order-102');
        $this->assertCount(1, $fetched->items);
        $this->assertEquals(50000.0, $fetched->grandTotal);

        // Update Quantity
        $this->orderService->updateItemQuantity('order-102', 'item-201', 3);
        $fetched = $this->orderService->getOrder('order-102');
        $this->assertEquals(150000.0, $fetched->grandTotal);

        // Remove Item
        $this->orderService->removeItem('order-102', 'item-201');
        $fetched = $this->orderService->getOrder('order-102');
        $this->assertCount(0, $fetched->items);
        $this->assertEquals(0.0, $fetched->grandTotal);
    }
}
