<?php

return [
    'admin' => [
        'sales' => [
            'orders' => [
                'view' => [
                    'cancel-invoice'         => 'Cancel Invoice',
                    'confirm-cancel-invoice' => 'Are you sure you want to cancel this invoice?',
                    'edit'                   => 'Edit',
                ],

                'edit' => [
                    'edit'           => 'Edit',
                    'title'          => 'Edit Order',
                    'update-btn'     => 'Update',
                    'update-qty'     => 'Update Quantity',
                    'remove-item'    => 'Remove Item',
                    'update'         => 'Update',
                    'update-success' => 'Order updated successfully.',

                    'search' => [
                        'add-product' => 'Add Product',
                    ],
                ],
            ],

            'invoices' => [
                'cancel-success' => 'Invoice cancelled successfully.',
                'cancel-failure' => 'Invoice cannot be cancelled.',

                'index' => [
                    'datagrid' => [
                        'delete' => 'Delete Invoice',
                    ],
                ],
            ],
        ],
    ],
];
