<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-[calc(100vh-140px)]">
    <!-- Left Section: Product Grid (2/3 width on large screens) -->
    <div class="lg:col-span-2 flex flex-col space-y-4 h-full">
        <!-- Search bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="searchProduct" type="text" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md" placeholder="Search products by code or name...">
            </div>
        </div>

        <!-- Session Message / Toast -->
        @if(session()->has('error'))
            <div class="p-3 bg-red-50 border-l-4 border-red-400 text-red-700 text-sm rounded shadow-sm">
                {{ session('error') }}
            </div>
        @endif
        @if(session()->has('success'))
            <div class="p-3 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 text-sm rounded shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Product Cards (Scrollable container) -->
        <div class="flex-1 overflow-y-auto bg-gray-50 border border-gray-200 rounded-lg p-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                @forelse($products as $product)
                    <div wire:click="addToCart({{ $product->id }})" class="bg-white rounded-lg border border-gray-200 p-3 flex flex-col justify-between hover:border-blue-500 hover:shadow-md cursor-pointer transition-all duration-200">
                        <div>
                            <!-- Placeholder image representing product visually -->
                            <div class="w-full h-24 bg-gray-100 rounded-md flex items-center justify-center mb-2 font-bold font-mono text-gray-400 text-sm">
                                {{ $product->code }}
                            </div>
                            <h4 class="font-semibold text-gray-900 text-xs sm:text-sm truncate" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h4>
                            <p class="text-xxs text-gray-500 mt-0.5">
                                Stock: <span class="font-bold @if($product->stock > 0) text-gray-700 @else text-red-500 @endif">{{ $product->stock }} {{ $product->unit }}</span>
                            </p>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-600 font-mono">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </span>
                            <span class="p-1 bg-blue-50 text-blue-600 rounded-full hover:bg-blue-600 hover:text-white transition-colors duration-150">
                                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 italic">
                        No products found matching your search.
                    </div>
                @endforelse
            </div>
            
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Right Section: Cart and Checkout (1/3 width) -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 flex flex-col h-full overflow-hidden">
        <!-- Panel Header -->
        <div class="p-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
            <h3 class="font-bold text-gray-800 text-md">Shopping Cart</h3>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                {{ count($cart) }} Items
            </span>
        </div>

        <!-- Customer selection -->
        <div class="p-4 border-b border-gray-100 space-y-2">
            <label for="customerId" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Customer</label>
            <select wire:model.live="customerId" id="customerId" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="">Walk-in Customer</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->id }}">{{ $cust->name }} (@if($cust->company_name) {{ $cust->company_name }} @else Retail @endif)</option>
                @endforeach
            </select>
        </div>

        <!-- Cart Items (Scrollable) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($cart as $item)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div class="flex-1 min-w-0 pr-2">
                        <p class="text-xs font-bold text-gray-900 truncate">{{ $item['name'] }}</p>
                        <p class="text-xxs font-mono text-blue-600 mt-0.5">
                            Rp {{ number_format($item['price'], 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="updateQty({{ $item['id'] }}, {{ $item['qty'] - 1 }})" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs font-bold">
                            -
                        </button>
                        <span class="w-8 text-center text-xs font-mono font-bold">{{ $item['qty'] }}</span>
                        <button wire:click="updateQty({{ $item['id'] }}, {{ $item['qty'] + 1 }})" class="p-1 bg-gray-100 text-gray-600 rounded hover:bg-gray-200 text-xs font-bold">
                            +
                        </button>
                        <button wire:click="removeFromCart({{ $item['id'] }})" class="p-1 text-red-500 hover:text-red-700 rounded text-xs font-bold ml-2">
                            ×
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-20 text-gray-400 italic text-xs">
                    Cart is empty. Click products on the left side to add them here.
                </div>
            @endforelse
        </div>

        <!-- Payment & Summary Section -->
        <div class="p-4 border-t border-gray-200 bg-gray-50 space-y-4">
            <!-- Calculation totals -->
            @php
                $subtotal = 0;
                foreach($cart as $item) {
                    $subtotal += $item['price'] * $item['qty'];
                }
            @endphp
            <div class="space-y-1.5 text-xs text-gray-600">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span class="font-mono">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between border-t border-gray-200 pt-2 text-base font-bold text-gray-900">
                    <span>Total Amount</span>
                    <span class="font-mono text-blue-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment parameters -->
            <div class="grid grid-cols-2 gap-3 pt-2">
                <div>
                    <label for="paymentMethod" class="block text-xxs font-semibold text-gray-500 uppercase tracking-wider">Payment Method</label>
                    <select wire:model.live="paymentMethod" id="paymentMethod" class="mt-1 block w-full py-1.5 px-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-xs">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank / QRIS</option>
                    </select>
                </div>
                <div>
                    <label for="cashPaid" class="block text-xxs font-semibold text-gray-500 uppercase tracking-wider">Cash Received</label>
                    <input wire:model.live="cashPaid" type="number" id="cashPaid" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm text-xs border-gray-300 rounded-md" min="0">
                </div>
            </div>

            <!-- Change due indicator -->
            @if($paymentMethod === 'cash')
                <div class="flex justify-between text-xs text-gray-500 bg-gray-100 p-2 rounded">
                    <span>Change Due</span>
                    <span class="font-mono font-bold text-gray-900">Rp {{ number_format(max(0, $cashPaid - $subtotal), 0, ',', '.') }}</span>
                </div>
            @endif

            <!-- Submit action -->
            <button wire:click="checkout" @if(empty($cart)) disabled @endif class="w-full py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors duration-150">
                PAY & CHECKOUT
            </button>
        </div>
    </div>
</div>
