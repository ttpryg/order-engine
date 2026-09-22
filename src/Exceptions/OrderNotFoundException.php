<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Exceptions;

use RuntimeException;

class OrderNotFoundException extends RuntimeException
{
    public static function forId(string $id): self
    {
        return new self("Order not found with ID: {$id}");
    }

    public static function forOrderNumber(string $orderNumber): self
    {
        return new self("Order not found with Order Number: {$orderNumber}");
    }
}
