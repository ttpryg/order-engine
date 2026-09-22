<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Exceptions;

use RuntimeException;
use Ttpryg\OrderEngine\Enums\OrderStatus;

class InvalidStatusTransitionException extends RuntimeException
{
    public static function create(OrderStatus $from, OrderStatus $to): self
    {
        return new self("Cannot transition order status from '{$from->value}' to '{$to->value}'");
    }
}
