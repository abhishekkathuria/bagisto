<?php

namespace Webkul\OrderManagement\Http\Controllers\Admin\Sales;

use Illuminate\Http\Response;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Sales\Repositories\OrderCommentRepository;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Admin\Http\Controllers\Sales\OrderController as BaseOrderController;

class OrderController extends BaseOrderController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderCommentRepository $orderCommentRepository,
        protected CartRepository $cartRepository,
        protected CustomerGroupRepository $customerGroupRepository,
    ) {}

    /**
     * Store order
     */
    public function store(int $cartId)
    {
        $cart = $this->cartRepository->findOrFail($cartId);

        Cart::setCart($cart);

        if (Cart::hasError()) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.error'),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        Cart::collectTotals();

        try {
            $this->validateOrder();
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart = Cart::getCart();

        if (! in_array($cart->payment->method, ['cashondelivery', 'moneytransfer'])) {
            return response()->json([
                'message' => trans('admin::app.sales.orders.create.payment-not-supported'),
            ], Response::HTTP_BAD_REQUEST);
        }

        $data = (new OrderResource($cart))->jsonSerialize();

        $order = $this->orderRepository->create($data);

        // Cart::removeCart($cart);

        session()->flash('order', trans('admin::app.sales.orders.create.order-placed-success'));

        return new JsonResource([
            'redirect'     => true,
            'redirect_url' => route('admin.sales.orders.view', $order->id),
        ]);
    }
}
