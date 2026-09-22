<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Ttpryg\EventDispatcher\Dispatcher\EventDispatcher;
use Ttpryg\EventDispatcher\Provider\ListenerProvider;
use Ttpryg\OrderEngine\Entities\Order;
use Ttpryg\OrderEngine\Enums\OrderStatus;
use Ttpryg\OrderEngine\Events\OrderCancelledEvent;
use Ttpryg\OrderEngine\Exceptions\InvalidStatusTransitionException;
use Ttpryg\OrderEngine\Repositories\MemoryOrderHistoryRepository;
use Ttpryg\OrderEngine\Repositories\MemoryOrderRepository;
use Ttpryg\OrderEngine\Services\OrderStatusService;

class OrderStatusServiceTest extends TestCase
{
    private MemoryOrderRepository $orderRepo;

    private MemoryOrderHistoryRepository $historyRepo;

    private ListenerProvider $listenerProvider;

    private OrderStatusService $statusService;

    protected function setUp(): void
    {
        $this->orderRepo = new MemoryOrderRepository;
        $this->historyRepo = new MemoryOrderHistoryRepository;
        $this->listenerProvider = new ListenerProvider;
        $dispatcher = new EventDispatcher($this->listenerProvider);

        $this->statusService = new OrderStatusService(
            $this->orderRepo,
            $this->historyRepo,
            $dispatcher
        );
    }

    public function test_valid_status_lifecycle_transitions(): void
    {
        $order = new Order(
            id: 'ord-test-1',
            orderNumber: 'ORD-001',
            customerType: 'user',
            customerId: 'user-100',
            status: OrderStatus::PENDING
        );
        $this->orderRepo->save($order);

        // PENDING -> CONFIRMED
        $confirmedOrder = $this->statusService->confirm('ord-test-1');
        $this->assertEquals(OrderStatus::CONFIRMED, $confirmedOrder->status);

        // CONFIRMED -> PROCESSING
        $processingOrder = $this->statusService->process('ord-test-1');
        $this->assertEquals(OrderStatus::PROCESSING, $processingOrder->status);

        // PROCESSING -> COMPLETED
        $completedOrder = $this->statusService->complete('ord-test-1');
        $this->assertEquals(OrderStatus::COMPLETED, $completedOrder->status);

        // COMPLETED -> REFUNDED
        $refundedOrder = $this->statusService->refund('ord-test-1', reason: 'Customer return');
        $this->assertEquals(OrderStatus::REFUNDED, $refundedOrder->status);

        $histories = $this->historyRepo->findByOrderId('ord-test-1');
        $this->assertCount(4, $histories);
    }

    public function test_invalid_status_transition_throws_exception(): void
    {
        $order = new Order(
            id: 'ord-test-2',
            orderNumber: 'ORD-002',
            customerType: 'user',
            customerId: 'user-100',
            status: OrderStatus::PENDING
        );
        $this->orderRepo->save($order);

        $this->expectException(InvalidStatusTransitionException::class);
        $this->statusService->complete('ord-test-2');
    }

    public function test_idempotent_status_transition(): void
    {
        $order = new Order(
            id: 'ord-test-3',
            orderNumber: 'ORD-003',
            customerType: 'user',
            customerId: 'user-100',
            status: OrderStatus::CONFIRMED
        );
        $this->orderRepo->save($order);

        // Confirming an already confirmed order
        $this->statusService->confirm('ord-test-3');
        $this->assertEquals(OrderStatus::CONFIRMED, $order->status);

        $histories = $this->historyRepo->findByOrderId('ord-test-3');
        $this->assertCount(0, $histories);
    }

    public function test_cancel_order_dispatches_event(): void
    {
        $eventDispatched = false;
        $this->listenerProvider->addListener(OrderCancelledEvent::class, function (OrderCancelledEvent $event) use (&$eventDispatched) {
            $eventDispatched = true;
            $this->assertEquals('ord-test-4', $event->order->id);
            $this->assertEquals('Out of stock', $event->reason);
        });

        $order = new Order(
            id: 'ord-test-4',
            orderNumber: 'ORD-004',
            customerType: 'user',
            customerId: 'user-100',
            status: OrderStatus::PENDING
        );
        $this->orderRepo->save($order);

        $this->statusService->cancel('ord-test-4', reason: 'Out of stock');
        $this->assertTrue($eventDispatched);
    }
}
