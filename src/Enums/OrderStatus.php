<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function canTransitionTo(self $target): bool
    {
        if ($this === $target) {
            return true;
        }

        return match ($this) {
            self::PENDING => in_array($target, [self::CONFIRMED, self::CANCELLED], true),
            self::CONFIRMED => in_array($target, [self::PROCESSING, self::CANCELLED], true),
            self::PROCESSING => in_array($target, [self::COMPLETED, self::CANCELLED], true),
            self::COMPLETED => $target === self::REFUNDED,
            self::CANCELLED, self::REFUNDED => false,
        };
    }
}
