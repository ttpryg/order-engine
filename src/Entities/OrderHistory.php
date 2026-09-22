<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Entities;

use DateTimeImmutable;
use Ttpryg\OrderEngine\Enums\OrderHistoryAction;
use Ttpryg\OrderEngine\Enums\OrderStatus;

class OrderHistory
{
    public readonly OrderHistoryAction $action;

    public readonly ?OrderStatus $fromStatus;

    public readonly ?OrderStatus $toStatus;

    public function __construct(
        public readonly string $id,
        public readonly string $orderId,
        OrderHistoryAction|string $action,
        OrderStatus|string|null $fromStatus = null,
        OrderStatus|string|null $toStatus = null,
        public ?string $referenceType = null,
        public ?string $referenceId = null,
        public ?string $note = null,
        public ?string $actorId = null,
        public ?DateTimeImmutable $createdAt = null
    ) {
        $this->action = is_string($action) ? OrderHistoryAction::from($action) : $action;
        $this->fromStatus = is_string($fromStatus) ? OrderStatus::from($fromStatus) : $fromStatus;
        $this->toStatus = is_string($toStatus) ? OrderStatus::from($toStatus) : $toStatus;
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
    }
}
