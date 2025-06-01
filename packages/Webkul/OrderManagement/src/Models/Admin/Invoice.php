<?php

namespace Webkul\OrderManagement\Models\Admin;

use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\Invoice as BaseInvoice;

class Invoice extends BaseInvoice
{
    /**
     * Checks if Invoice can be canceled or not
     */
    public function canCancel(): bool
    {
        if (
            in_array($this->order->status, [
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
