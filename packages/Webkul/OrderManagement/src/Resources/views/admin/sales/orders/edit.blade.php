<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
    </x-slot>

    <!-- Header -->
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">

            <div class="flex items-center gap-2.5">
                <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                    @lang('admin::app.sales.orders.view.title', ['order_id' => $order->increment_id])
                </p>

                <!-- Order Status -->
                <span class="label-{{ $order->status }} text-sm mx-1.5">
                    @lang("admin::app.sales.orders.view.$order->status")
                </span>
            </div>

            <!-- Back Button -->
            <a
                href="{{ route('admin.sales.orders.index') }}"
                class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
            >
                @lang('admin::app.account.edit.back-btn')
            </a>
        </div>
    </div>

    <div class="mt-5 flex-wrap items-center justify-between gap-x-1 gap-y-2">
        <!-- Order details -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
            <!-- Left Component -->
             <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                <div class="box-shadow rounded bg-white dark:bg-gray-900">
                    <v-edit-order>
                    </v-edit-order>
                </div>
            </div>

            <!-- Right Component -->
            <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">
                {!! view_render_event('bagisto.admin.sales.order.right_component.before', ['order' => $order]) !!}

                <!-- Customer and address information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.customer')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="{{ $order->billing_address ? 'pb-4' : '' }}">
                            <div class="flex flex-col gap-1.5">
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $order->customer_full_name }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_full_name.after', ['order' => $order]) !!}

                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $order->customer_email }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_email.after', ['order' => $order]) !!}

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.customer-group') : {{ $order->is_guest ? core()->getGuestCustomerGroup()?->name : ($order->customer->group->name ?? '') }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.customer_group.after', ['order' => $order]) !!}
                            </div>
                        </div>

                        <!-- Billing Address -->
                        @if ($order->billing_address)
                            <span class="block w-full border-b dark:border-gray-800"></span>

                            <div class="{{ $order->shipping_address ? 'pb-4' : '' }}">

                                <div class="flex items-center justify-between">
                                    <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.billing-address')
                                    </p>
                                </div>

                                @include ('admin::sales.address', ['address' => $order->billing_address])

                                {!! view_render_event('bagisto.admin.sales.order.billing_address.after', ['order' => $order]) !!}
                            </div>
                        @endif

                        <!-- Shipping Address -->
                        @if ($order->shipping_address)
                            <span class="block w-full border-b dark:border-gray-800"></span>

                            <div class="flex items-center justify-between">
                                <p class="py-4 text-base font-semibold text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-address')
                                </p>
                            </div>

                            @include ('admin::sales.address', ['address' => $order->shipping_address])

                            {!! view_render_event('bagisto.admin.sales.order.shipping_address.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                <!-- Order Information -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.order-information')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex w-full justify-start gap-5">
                            <div class="flex flex-col gap-y-1.5">
                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-date')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.order-status')
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.channel')
                                </p>
                            </div>

                            <div class="flex flex-col gap-y-1.5">
                                {!! view_render_event('bagisto.admin.sales.order.created_at.before', ['order' => $order]) !!}

                                <!-- Order Date -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{core()->formatDate($order->created_at) }}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.created_at.after', ['order' => $order]) !!}

                                <!-- Order Status -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->status_label}}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.status_label.after', ['order' => $order]) !!}

                                <!-- Order Channel -->
                                <p class="text-gray-600 dark:text-gray-300">
                                    {{$order->channel_name}}
                                </p>

                                {!! view_render_event('bagisto.admin.sales.order.channel_name.after', ['order' => $order]) !!}
                            </div>
                        </div>
                    </x-slot>
                </x-admin::accordion>

                <!-- Payment and Shipping Information-->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-600 dark:text-gray-300">
                            @lang('admin::app.sales.orders.view.payment-and-shipping')
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div>
                            <!-- Payment method -->
                            <p class="font-semibold text-gray-800 dark:text-white">
                                {{ core()->getConfigData('sales.payment_methods.' . $order->payment->method . '.title') }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.payment-method')
                            </p>

                            <!-- Currency -->
                            <p class="pt-4 font-semibold text-gray-800 dark:text-white">
                                {{ $order->order_currency_code }}
                            </p>

                            <p class="text-gray-600 dark:text-gray-300">
                                @lang('admin::app.sales.orders.view.currency')
                            </p>

                            @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

                            <!-- Additional details -->
                            @if (! empty($additionalDetails))
                                <p class="pt-4 font-semibold text-gray-800 dark:text-white">
                                    {{ $additionalDetails['title'] }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    {{ $additionalDetails['value'] }}
                                </p>
                            @endif

                            {!! view_render_event('bagisto.admin.sales.order.payment-method.after', ['order' => $order]) !!}
                        </div>

                        <!-- Shipping Method and Price Details -->
                        @if ($order->shipping_address)
                            <span class="mt-4 block w-full border-b dark:border-gray-800"></span>

                            <div class="pt-4">
                                <p class="font-semibold text-gray-800 dark:text-white">
                                    {{ $order->shipping_title }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-method')
                                </p>

                                <p class="pt-4 font-semibold text-gray-800 dark:text-white">
                                    {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                </p>

                                <p class="text-gray-600 dark:text-gray-300">
                                    @lang('admin::app.sales.orders.view.shipping-price')
                                </p>
                            </div>

                            {!! view_render_event('bagisto.admin.sales.order.shipping-method.after', ['order' => $order]) !!}
                        @endif
                    </x-slot>
                </x-admin::accordion>

                {!! view_render_event('bagisto.admin.sales.order.right_component.after', ['order' => $order]) !!}
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-edit-order-template"
        >
            <div>
                <div
                    v-if="! isOrderUpdating"
                    class="primary-button"
                    @click="update()"
                >
                    <span class="icon-cart text-2xl"></span>

                    @lang('order_management::app.admin.sales.orders.edit.update')
                </div>

                <div
                    v-else
                    class="secondary-button"
                >
                    <img
                        class="h-5 w-5 animate-spin"
                        src="{{ bagisto_asset('images/spinner.svg') }}"
                    />
                </div>

                <div class="flex justify-between p-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('Order Items') ({{ count($order->items) }})
                    </p>

                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                        @lang('admin::app.sales.orders.view.grand-total', ['grand_total' => core()->formatBasePrice($order->base_grand_total)])
                    </p>
                </div>

                <!-- Order items -->
                <div class="grid">
                    @foreach ($order->items as $item)
                        <label class="flex items-center gap-2 cursor-pointer">
                            @lang('order_management::app.admin.sales.orders.edit.remove-item')
                            
                            <input
                                type="checkbox"
                                class="form-checkbox"
                                :true-value="true"
                                :false-value="false"
                                name="isRemoved{{ $item->id }}"
                                @click="removeItem({{ $item->id }}, 'order')"
                            >
                        </label>

                        <div class="flex justify-between gap-2.5 border-b border-slate-300 px-4 py-6 dark:border-gray-800">
                            <div class="flex gap-2.5">
                                @if($item?->product?->base_image_url)
                                    <img
                                        class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                        src="{{ $item?->product->base_image_url }}"
                                    >
                                @else
                                    <div class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert">
                                        <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">

                                        <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                            @lang('admin::app.sales.invoices.view.product-image')
                                        </p>
                                    </div>
                                @endif

                                <div class="grid place-content-start gap-1.5">
                                    <p class="break-all text-base font-semibold text-gray-800 dark:text-white">
                                        {{ $item->name }}
                                    </p>

                                    <div class="flex flex-col place-items-start gap-1.5">
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.amount-per-unit', [
                                                'amount' => core()->formatBasePrice($item->base_price),
                                                'qty'    => $item->qty_ordered,
                                            ])
                                        </p>

                                        @if (isset($item->additional['attributes']))
                                            @foreach ($item->additional['attributes'] as $attribute)
                                                <p class="text-gray-600 dark:text-gray-300">
                                                    @if (
                                                        ! isset($attribute['attribute_type'])
                                                        || $attribute['attribute_type'] !== 'file'
                                                    )
                                                        {{ $attribute['attribute_name'] }} : {{ $attribute['option_label'] }}
                                                    @else
                                                        {{ $attribute['attribute_name'] }} :

                                                        <a
                                                            href="{{ Storage::url($attribute['option_label']) }}"
                                                            class="text-blue-600 hover:underline"
                                                            download="{{ File::basename($attribute['option_label']) }}"
                                                        >
                                                            {{ File::basename($attribute['option_label']) }}
                                                        </a>
                                                    @endif
                                                </p>
                                            @endforeach
                                        @endif

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.sku', ['sku' => $item->sku])
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            {{ $item->qty_ordered ? trans('admin::app.sales.orders.view.item-ordered', ['qty_ordered' => $item->qty_ordered]) : '' }}

                                            {{ $item->qty_invoiced ? trans('admin::app.sales.orders.view.item-invoice', ['qty_invoiced' => $item->qty_invoiced]) : '' }}

                                            {{ $item->qty_shipped ? trans('admin::app.sales.orders.view.item-shipped', ['qty_shipped' => $item->qty_shipped]) : '' }}

                                            {{ $item->qty_refunded ? trans('admin::app.sales.orders.view.item-refunded', ['qty_refunded' => $item->qty_refunded]) : '' }}

                                            {{ $item->qty_canceled ? trans('admin::app.sales.orders.view.item-canceled', ['qty_canceled' => $item->qty_canceled]) : '' }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Quantity input field -->
                                 <x-admin::form.control-group>
                                    <x-admin::form.control-group.label for="qty-{{ $item->id }}">
                                        @lang('order_management::app.admin.sales.orders.edit.update-qty')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        id="qty-{{ $item->id }}"
                                        type="number"
                                        min="1"
                                        class="form-input w-20"
                                        value="{{ $item->qty_ordered }}"
                                        @change="updateQty({{ $item->id }})"
                                    />
                                </x-admin::form.control-group>
                            </div>

                            <div class="grid place-content-start gap-1">
                                <div class="">
                                    <p class="flex items-center justify-end gap-x-1 text-base font-semibold text-gray-800 dark:text-white">
                                        {{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                    </p>
                                </div>

                                <div class="flex flex-col place-items-start items-end gap-1.5">
                                    @if (core()->getConfigData('sales.taxes.sales.display_prices') == 'including_tax')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                        </p>
                                    @elseif (core()->getConfigData('sales.taxes.sales.display_prices') == 'both')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.price-excl-tax', ['price' => core()->formatBasePrice($item->base_price)])
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.price-incl-tax', ['price' => core()->formatBasePrice($item->base_price_incl_tax)])
                                        </p>
                                    @else
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.price', ['price' => core()->formatBasePrice($item->base_price)])
                                        </p>
                                    @endif

                                    <p class="text-gray-600 dark:text-gray-300">
                                        @lang('admin::app.sales.orders.view.tax', [
                                            'percent' => number_format($item->tax_percent, 2) . '%',
                                            'tax'     => core()->formatBasePrice($item->base_tax_amount)
                                        ])
                                    </p>

                                    @if ($order->base_discount_amount > 0)
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.discount', ['discount' => core()->formatBasePrice($item->base_discount_amount)])
                                        </p>
                                    @endif

                                    @if (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'including_tax')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                        </p>
                                    @elseif (core()->getConfigData('sales.taxes.sales.display_subtotal') == 'both')
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.sub-total-excl-tax', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.sub-total-incl-tax', ['sub_total' => core()->formatBasePrice($item->base_total_incl_tax)])
                                        </p>
                                    @else
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @lang('admin::app.sales.orders.view.sub-total', ['sub_total' => core()->formatBasePrice($item->base_total)])
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button
                    class="secondary-button flex justify-between mt-4"
                    @click="$refs.searchProductDrawer.open()"
                >
                    @lang('admin::app.sales.orders.create.cart.items.add-product')
                </button>

                <div class="flex justify-between p-4">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('Order Items') (@{{ newOrder.items.length }})
                    </p>

                    <p class="text-base font-semibold text-gray-800 dark:text-white">
                        @{{ "@lang('admin::app.sales.orders.view.grand-total')".replace(':grand_total', formatePrice(newOrder.base_grand_total)) }}
                    </p>
                </div>
                
                <template v-for="item in newOrder.items">
                    <div class="grid" v-if="! item.isRemoved">
                        <div class="flex justify-between gap-2.5 border-b border-slate-300 px-4 py-6 dark:border-gray-800">
                            <div class="flex gap-2.5">
                                <img
                                    v-if="item?.product?.images[0]?.url"
                                    class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                    :src="item?.product.images[0].url"
                                >

                                <div
                                    v-else
                                    class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert"
                                >
                                    <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">

                                    <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                        @lang('admin::app.sales.invoices.view.product-image')
                                    </p>
                                </div>

                                <div class="grid place-content-start gap-1.5">
                                    <p class="break-all text-base font-semibold text-gray-800 dark:text-white">
                                        @{{ item.product.name }}
                                    </p>

                                    <div class="flex flex-col place-items-start gap-1.5">
                                        <p class="text-gray-600 dark:text-gray-300">
                                            @{{ "@lang('admin::app.sales.orders.view.amount-per-unit')".replace(':amount', formatePrice(item.product.price)).replace(':qty', item.qty) }}
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @{{ "@lang('admin::app.sales.orders.view.sku')".replace(':sku', item.product.sku) }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                                    @click="removeItem(item.id, 'newOrder')"
                                >
                                    <span class="icon-cancel text-2xl"></span>

                                    @lang('order_management::app.admin.sales.orders.edit.remove-item')
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Search Drawer -->
                <x-admin::drawer
                    ref="searchProductDrawer"
                    @close="searchTerm = ''; searchedProducts = [];"
                >
                    <!-- Drawer Header -->
                    <x-slot:header>
                        <div class="grid gap-5">
                            <p class="text-xl font-medium dark:text-white">
                                @lang('admin::app.sales.orders.create.cart.items.search.title')
                            </p>

                            <div class="relative w-full">
                                <input
                                    type="text"
                                    class="block w-full rounded-lg border bg-white py-1.5 leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 ltr:pl-3 ltr:pr-10 rtl:pl-10 rtl:pr-3"
                                    placeholder="Search by name"
                                    v-model.lazy="searchTerm"
                                    v-debounce="500"
                                />

                                <template v-if="isSearching">
                                    <img
                                        class="absolute top-2.5 h-5 w-5 animate-spin ltr:right-3 rtl:left-3"
                                        src="{{ bagisto_asset('images/spinner.svg') }}"
                                    />
                                </template>

                                <template v-else>
                                    <span class="icon-search pointer-events-none absolute top-1.5 flex items-center text-2xl ltr:right-3 rtl:left-3"></span>
                                </template>
                            </div>
                        </div>
                    </x-slot>

                    <!-- Drawer Content -->
                    <x-slot:content class="!p-0">
                        <div
                            class="grid"
                            v-if="searchedProducts.length"
                        >
                            <div
                                class="flex justify-between gap-2.5 border-b border-slate-300 px-4 py-6 dark:border-gray-800"
                                v-for="product in searchedProducts"
                            >
                                <!-- Information -->
                                <div class="flex gap-2.5">
                                    <!-- Image -->
                                    <div
                                        class="relative h-[60px] max-h-[60px] w-full max-w-[60px] overflow-hidden rounded"
                                        :class="{'border border-dashed border-gray-300 dark:border-gray-800 dark:mix-blend-exclusion dark:invert': ! product.images.length}"
                                    >
                                        <template v-if="! product.images.length">
                                            <img
                                                class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded" 
                                                src="{{ bagisto_asset('images/product-placeholders/front.svg') }}"
                                            >
                                        
                                            <p class="absolute bottom-1.5 w-full text-center text-[6px] font-semibold text-gray-400">
                                                @lang('admin::app.sales.orders.create.cart.items.search.product-image')
                                            </p>
                                        </template>

                                        <template v-else>
                                            <img
                                                class="relative h-[60px] max-h-[60px] w-full max-w-[60px] rounded"
                                                :src="product.images[0].url"
                                            >
                                        </template>
                                    </div>

                                    <!-- Details -->
                                    <div class="grid place-content-start gap-1.5">
                                        <p class="break-all text-base font-semibold text-gray-800 dark:text-white">
                                            @{{ product.name }}
                                        </p>

                                        <p class="text-gray-600 dark:text-gray-300">
                                            @{{ "@lang('admin::app.sales.orders.create.cart.items.search.sku')".replace(':sku', product.sku) }}
                                        </p>

                                        <p class="text-green-600">
                                            @{{ "@lang('admin::app.sales.orders.create.cart.items.search.available-qty')".replace(':qty', availbleQty(product)) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <x-admin::form
                                    v-slot="{ meta, errors, handleSubmit }"
                                    as="div"
                                >
                                    <form @submit="handleSubmit($event, addProduct)">
                                        <div class="grid place-content-start gap-2 text-right">
                                            <p class="font-semibold text-gray-800 dark:text-white">
                                                @{{ product.formatted_price }}
                                            </p>

                                            <x-admin::form.control-group class="!mb-0">
                                                <x-admin::form.control-group.label class="required justify-end">
                                                    @lang('admin::app.sales.orders.create.cart.items.search.qty')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="hidden"
                                                    name="product_id"
                                                    ::value="product.id"
                                                />

                                                <x-admin::form.control-group.control
                                                    type="number"
                                                    name="qty"
                                                    class="!w-20 !px-2 !py-1.5"
                                                    value=""
                                                    rules="required|numeric|min_value:1"
                                                    :label="trans('admin::app.sales.orders.create.cart.items.search.qty')"
                                                    :placeholder="trans('admin::app.sales.orders.create.cart.items.search.qty')"
                                                />

                                                <x-admin::form.control-group.error name="qty" />
                                            </x-admin::form.control-group>

                                            <button
                                                class="cursor-pointer text-sm text-blue-600 transition-all hover:underline"
                                                :disabled="! product.is_saleable"
                                            >
                                                @lang('order_management::app.admin.sales.orders.edit.search.add-product')
                                            </button>
                                        </div>
                                    </form>
                                </x-admin::form>
                            </div>
                        </div>

                        <!-- For Empty Variations -->
                        <div
                            class="grid justify-center justify-items-center gap-3.5 px-2.5 py-10"
                            v-else
                        >
                            <!-- Placeholder Image -->
                            <img
                                src="{{ bagisto_asset('images/icon-add-product.svg') }}"
                                class="h-20 w-20 dark:mix-blend-exclusion dark:invert"
                            />

                            <!-- Add Variants Information -->
                            <div class="flex flex-col items-center gap-1.5">
                                <p class="text-base font-semibold text-gray-400">
                                    @lang('admin::app.sales.orders.create.cart.items.search.empty-title')
                                </p>

                                <p class="text-gray-400">
                                    @lang('admin::app.sales.orders.create.cart.items.search.empty-info')
                                </p>
                            </div>
                        </div>
                    </x-slot>
                </x-admin::drawer>
            </div>
        </script>

        <script type="module">
            app.component('v-edit-order', {
                template: '#v-edit-order-template',

                data() {
                    return {
                        order: {
                            items: {},
                        },

                        newOrder: {
                            items: {},
                            base_grand_total: 0,
                        },

                        availableProducts: {
                            items: {},
                        },

                        searchTerm: '',

                        searchedProducts: [],

                        isSearching: false,

                        isOrderUpdating: false,
                    };
                },

                watch: {
                    searchTerm: function(newVal, oldVal) {
                        this.search();
                    }
                },

                mounted() {
                    @foreach ($order->items as $item)
                        this.availableProducts[{{ $item->product_id }}] = {
                            id: {{ $item->id }},
                        };

                        this.order.items[{{ $item->id }}] = {
                            id: {{ $item->id }},
                            qty: {{ $item->qty_ordered }},
                            isRemoved: false,
                            isUpdated: false,
                        };
                    @endforeach
                },

                methods: {
                    removeItem(itemId, type) {
                        if (type == 'order') {
                            this.order.items[itemId]['isRemoved'] = ! this.order.items[itemId]['isRemoved'];

                            this.order.items[itemId]['isUpdated'] = true;
                        }

                        if (type == 'newOrder') {
                            console.log(this.newOrder.items);
                            console.log(itemId);
                            this.newOrder.items[itemId]['isRemoved'] = true;

                            this.newOrder.base_grand_total -= this.newOrder.items[itemId].product.price * this.newOrder.items[itemId].qty;

                            console.log(this.newOrder.items);
                        }
                    },

                    updateQty(itemId) {
                        this.order.items[itemId]['qty'] = document.getElementById('qty-' + itemId).value;

                        this.order.items[itemId]['isUpdated'] = true;
                    },

                    search() {
                        if (this.searchTerm.length <= 1) {
                            this.searchedProducts = [];

                            return;
                        }

                        this.isSearching = true;

                        let self = this;
                        
                        this.$axios.get("{{ route('admin.catalog.products.search') }}", {
                                params: {
                                    query: this.searchTerm,
                                    customer_id: {{ $order->customer_id ?? 'null' }},
                                    type: 'simple',
                                }
                            })
                            .then(function(response) {
                                self.isSearching = false;

                                self.searchedProducts = response.data.data;
                            })
                            .catch(function (error) {
                            });
                    },

                    availbleQty(product) {
                        let qty = 0;

                        product.inventories.forEach(function (inventory) {
                            qty += inventory.qty;
                        });

                        return qty;
                    },

                    addProduct(params) {
                        if (this.availableProducts[params.product_id]) {
                            this.order.items[[this.availableProducts[params.product_id]['id']]]['isUpdated'] = true;

                            
                            this.order.items[this.availableProducts[params.product_id]['id']]['qty'] += params.qty;
                            this.order.items[this.availableProducts[params.product_id]['id']]['isRemoved'] = false;

                            document.getElementById('qty-' + this.availableProducts[params.product_id]['id']).value = this.order.items[this.availableProducts[params.product_id]['id']]['qty'];
                            // Uncheck the "isRemoved" checkbox if it was checked
                            const checkbox = document.querySelector(`input[name="isRemoved${this.availableProducts[params.product_id]['id']}"]`);
                            if (checkbox) {
                                checkbox.checked = false;
                            }
                        } else {
                            this.newOrder.items[params.product_id] = {
                                id: params.product_id,
                                qty: params.qty,
                                isRemoved: false,
                                product: this.searchedProducts.find(product => product.id == params.product_id),
                            };
                            
                            this.newOrder.base_grand_total += this.newOrder.items[params.product_id].product.price * params.qty;
                        }
                        
                        this.$refs.searchProductDrawer.close();
                    },

                    formatePrice(price) {
                        // Use JavaScript to format as currency, fallback to 2 decimals
                        return new Intl.NumberFormat('{{ app()->getLocale() }}', { style: 'currency', currency: '{{ $order->order_currency_code }}' }).format(price ?? 0);
                    },

                    update() {
                        this.isOrderUpdating = true;

                        this.$axios.put("{{ route('order_management.admin.sales.orders.update', $order->id) }}", {
                            order_items: this.order.items,
                            new_items: this.newOrder.items,
                        })
                        .then(response => {
                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            window.location.reload();
                        })
                        .catch(error => {
                            this.$emitter.emit('add-flash', { type: 'error', message: error.response?.data?.message || 'Update failed.' });

                            this.isOrderUpdating = false;
                        });
                    }
                },
            });
        </script>
    @endPushOnce
</x-admin::layouts>
