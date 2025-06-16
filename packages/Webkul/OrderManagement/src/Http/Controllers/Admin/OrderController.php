<?php

namespace Webkul\OrderManagement\Http\Controllers\Admin;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderItemResource;
use Webkul\Sales\Transformers\OrderResource;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected InvoiceRepository $invoiceRepository,
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected CartRepository $cartRepository,
        protected CartItemRepository $cartItemRepository,
        protected ProductRepository $productRepository
    ) {}

    /**
     * Show the view for the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $order = $this->orderRepository->with('items')->findOrFail($id);

        return view('order_management::admin.sales.orders.edit', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(int $id)
    {
        $order = $this->orderRepository->findOrFail($id);

        $orderItems = request()->input('order_items');

        $newItems = request()->input('new_items');

        $cart = $this->cartRepository->findOrFail($order->cart_id);

        Cart::setCart($cart);

        foreach ($newItems as $item) {
            if (
                isset($item['isRemoved'])
                && $item['isRemoved']
            ) {
                continue;
            }

            $product = $this->productRepository->findOrFail($item['id']);

            $data = [
                'quantity'   => $item['qty'],
                'product_id' => $item['id'],
            ];

            $cart = Cart::addProduct($product, $data);
        }

        foreach ($order->items as $item) {
            $cartItem = $cart->items->where('product_id', $item->product_id)->first();

            if (
                isset($orderItems[$item->id])
                && $orderItems[$item->id]['isUpdated']
            ) {
                if (! $orderItems[$item->id]['isRemoved']) {
                    $data['qty'][$cartItem->id] = $orderItems[$item->id]['qty'];

                    Cart::updateItems($data);
                } else {
                    $this->orderItemRepository->delete($item->id);

                    $this->cartItemRepository->delete($cartItem->id);
                }
            }
        }

        Cart::collectTotals();

        Cart::refreshCart();

        $cart = Cart::getCart();

        $orderData = (new OrderResource($cart))->jsonSerialize();

        $order->update($orderData);

        $order->refresh();

        $this->orderRepository->collectTotals($order);

        $orderItemData = OrderItemResource::collection($cart->items)->jsonSerialize();

        foreach ($orderItemData as $item) {
            $orderItem = $order->items->where('product_id', $item['product_id'])->first();

            if ($orderItem) {
                $orderItem->update($item);
            } else {
                $orderItem = $this->orderItemRepository->create(array_merge($item, ['order_id' => $order->id]));
            }

            $orderItem->refresh();

            Event::dispatch('checkout.order.orderitem.save.after', $orderItem);

            $this->orderItemRepository->manageInventory($orderItem);
        }

        $order->refresh();

        $this->orderRepository->collectTotals($order);

        $this->createOrCancelInvoice($order);

        Event::dispatch('checkout.order.save.after', $order);

        return response()->json([
            'redirect_url' => route('admin.sales.orders.view', $order->id),
            'message'      => __('order_management::app.admin.sales.orders.edit.update-success'),
        ]);
    }

    /**
     * Create or cancel invoice based on order status.
     *
     * @param  \Webkul\Sales\Models\Order  $order
     * @return void
     */
    protected function createOrCancelInvoice($order)
    {
        $invoices = $order->invoices;

        foreach ($invoices as $invoice) {
            $invoice->delete();
        }

        foreach ($order->items as $orderItem) {
            $this->orderItemRepository->collectTotals($orderItem);
        }

        $this->orderRepository->collectTotals($order);

        if ($order->canInvoice()) {
            $this->invoiceRepository->create($this->prepareInvoiceData($order));
        }
    }

    /**
     * Prepares order's invoice data for creation
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return array
     */
    public function prepareInvoiceData($order)
    {
        $invoiceData = [
            'order_id' => $order->id,
        ];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
