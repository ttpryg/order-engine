<?php

declare(strict_types=1);

namespace Ttpryg\OrderEngine\Enums;

enum OrderHistoryAction: string
{
    case ORDER_CREATED = 'order_created';
    case STATUS_CHANGED = 'status_changed';
    case ITEM_ADDED = 'item_added';
    case ITEM_REMOVED = 'item_removed';
    case ITEM_QUANTITY_CHANGED = 'item_quantity_changed';
    case ADDRESS_CHANGED = 'address_changed';
}
