<?php

namespace Webkul\OrderManagement\DataGrids\Admin\Sales;

use Webkul\Admin\DataGrids\Sales\OrderInvoiceDataGrid as AdminOrderInvoiceDataGrid;

class OrderInvoiceDataGrid extends AdminOrderInvoiceDataGrid
{
    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('order_management::app.admin.sales.invoices.index.datagrid.delete'),
            'method' => 'DELETE',
            'url'    => function ($row) {
                return route('order_management.admin.sales.invoices.cancel', $row->id);
            },
        ]);

        if (bouncer()->hasPermission('sales.invoices.view')) {
            $this->addAction([
                'icon'   => 'icon-view',
                'title'  => trans('admin::app.sales.invoices.index.datagrid.view'),
                'method' => 'GET',
                'url'    => function ($row) {
                    return route('admin.sales.invoices.view', $row->id);
                },
            ]);
        }
    }
}
