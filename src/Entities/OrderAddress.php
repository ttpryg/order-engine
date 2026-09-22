<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Entities;

use DateTimeImmutable;
use Ttpryg\OrderEngine\Enums\OrderAddressType;

class OrderAddress
{
    public readonly OrderAddressType $type;

    public function __construct(
        public readonly string $id,
        public readonly string $orderId,
        OrderAddressType|string $type,
        public string $name,
        public string $phone,
        public string $address,
        public string $city,
        public string $province,
        public string $postalCode,
        public string $country = 'ID',
        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->type = is_string($type) ? OrderAddressType::from($type) : $type;
        $this->createdAt = $createdAt ?? new DateTimeImmutable;
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable;
    }
}
