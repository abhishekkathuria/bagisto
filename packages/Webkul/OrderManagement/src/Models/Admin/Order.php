<?php

namespace Webkul\OrderManagement\Models\Admin;

use Webkul\Sales\Models\Order as BaseOrder;

class Order extends BaseOrder
{
    /**
     * Checks if new invoice is allow or not
     */
    public function canEdit(): bool
    {
        if (
            in_array($this->status, [
                Order::STATUS_PENDING,
                Order::STATUS_PENDING_PAYMENT,
                Order::STATUS_PROCESSING,
            ])
        ) {
            return true;
        }

        return false;
    }
}
