<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Production Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Monitor manufacturing processes, track raw material consumption, and record finished goods.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            New Production Order
        </button>
    </div>

    <!-- Alerts -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by order number or product name...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Order Number</th>
                        <th class="py-3.5 px-6">Product To Produce</th>
                        <th class="py-3.5 px-6 text-right">Target Qty</th>
                        <th class="py-3.5 px-6 text-right">Est. Production Cost</th>
                        <th class="py-3.5 px-6">Timeline</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-semibold text-blue-600">{{ $order->order_number }}</td>
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $order->product->name }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900 font-medium">{{ number_format($order->quantity, 2) }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900">
                                Rp {{ number_format($order->total_cost, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-gray-600 text-xs">
                                {{ $order->start_date }} to {{ $order->end_date }}
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $order->status === 'planned' ? 'bg-gray-100 text-gray-800' : '' }}
                                    {{ $order->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'completed' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if($order->status !== 'completed' && $order->status !== 'cancelled')
                                    <button wire:click="edit({{ $order->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Update Status</button>
                                @else
                                    <span class="text-gray-400 font-medium">Archived</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                No production orders registered.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                            {{ $isEditMode ? 'Update Production Order' : 'Add New Production Order' }}
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 font-sans">
                            <!-- Product Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product To Produce</label>
                                <select wire:model.live="product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white" {{ $isEditMode ? 'disabled' : '' }}>
                                    <option value="">Select Product</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Target Qty -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Target Qty</label>
                                <input type="number" step="0.01" wire:model.live="quantity" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('quantity') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="planned">Planned</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed (Deducts materials stock, adds finished goods)</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>
                        </div>

                        <!-- Required Materials List -->
                        @if(count($materials) > 0)
                            <div class="mt-6 border-t border-gray-200 pt-6">
                                <h4 class="text-md font-medium text-gray-900 font-display mb-4">Required Materials (BOM Reference)</h4>
                                <div class="overflow-hidden border border-gray-200 rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead>
                                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                                <th class="py-2.5 px-4">Component / Material</th>
                                                <th class="py-2.5 px-4 text-right">Required Qty</th>
                                                <th class="py-2.5 px-4 text-right">Unit Cost</th>
                                                <th class="py-2.5 px-4 text-right">Total Est. Cost</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($materials as $mat)
                                                <tr>
                                                    <td class="py-2.5 px-4 text-gray-900 font-medium">{{ $mat['name'] }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-800">{{ number_format($mat['qty_required'], 2) }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-800">Rp {{ number_format($mat['unit_cost'], 0) }}</td>
                                                    <td class="py-2.5 px-4 text-right font-mono text-gray-900 font-semibold">Rp {{ number_format($mat['qty_required'] * $mat['unit_cost'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 mt-4 italic">No BOM recipe found for the selected finished product. Raw materials lists will be empty.</p>
                        @endif
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Save Order
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
