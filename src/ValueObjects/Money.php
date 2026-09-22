<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\ValueObjects;

use InvalidArgumentException;

final class Money
{
    public function __construct(
        public readonly float $amount,
        public readonly string $currency = 'IDR'
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Money amount cannot be negative.');
        }
    }

    public function add(Money $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->ensureSameCurrency($other);
        $result = $this->amount - $other->amount;

        return new self(max(0.0, $result), $this->currency);
    }

    public function multiply(int|float $multiplier): self
    {
        return new self($this->amount * $multiplier, $this->currency);
    }

    private function ensureSameCurrency(Money $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Cannot operate on different currencies: {$this->currency} and {$other->currency}");
        }
    }

    public static function zero(string $currency = 'IDR'): self
    {
        return new self(0.0, $currency);
    }
}
