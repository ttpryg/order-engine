<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\ValueObjects;

use InvalidArgumentException;

final class OrderNumber implements \Stringable
{
    public function __construct(public readonly string $value)
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException('Order number cannot be empty.');
        }
    }

    public static function generate(string $prefix = 'ORD'): self
    {
        $date = date('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        return new self("{$prefix}-{$date}-{$random}");
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
