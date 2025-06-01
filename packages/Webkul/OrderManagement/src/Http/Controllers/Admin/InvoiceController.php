<?php

namespace Webkul\OrderManagement\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderItemRepository;

class InvoiceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository
    ) {}

    /**
     * Show the view for the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function cancel(int $id)
    {
        $invoice = $this->invoiceRepository->findOrFail($id);

        if (! $invoice->canCancel()) {
            if (request()->ajax()) {
                return new JsonResponse([
                    'message' => trans('order_management::app.admin.sales.invoices.cancel-failure'),
                ], 500);
            }

            session()->flash('error', trans('order_management::app.admin.sales.invoices.cancel-failure'));

            return view('admin::sales.orders.view', compact('order'));
        }

        $order = $invoice->order;
        
        $invoice->delete();

        foreach ($order->items as $orderItem) {
            $this->orderItemRepository->collectTotals($orderItem);
        }

        $this->orderRepository->collectTotals($order);

        if (request()->ajax()) {
            return new JsonResponse([
                'message' => trans('order_management::app.admin.sales.invoices.cancel-success'),
            ]);
        }

        session()->flash('success', trans('order_management::app.admin.sales.invoices.cancel-success'));

        return view('admin::sales.orders.view', compact('order'));
    }
}
