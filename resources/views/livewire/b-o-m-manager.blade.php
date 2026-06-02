<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Bill of Materials (BOM)</h1>
            <p class="text-sm text-gray-500 mt-1">Define assembly structures, raw materials, and recipes for manufactured products.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add New BOM
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
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by finished product name...">
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Finished Product</th>
                        <th class="py-3.5 px-6">BOM Version</th>
                        <th class="py-3.5 px-6">Notes</th>
                        <th class="py-3.5 px-6 text-right">No. of Components</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($boms as $bom)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 text-gray-900 font-medium">{{ $bom->product->name }}</td>
                            <td class="py-4 px-6 font-mono text-gray-700">{{ $bom->bom_version }}</td>
                            <td class="py-4 px-6 text-gray-600">{{ $bom->notes ?: '-' }}</td>
                            <td class="py-4 px-6 text-right font-mono text-gray-900">
                                {{ $bom->items->count() }} items
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <button wire:click="edit({{ $bom->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                <button wire:click="delete({{ $bom->id }})" wire:confirm="Are you sure you want to delete this BOM?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                No Bills of Materials defined.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($boms->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $boms->links() }}
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
                            {{ $isEditMode ? 'Edit BOM' : 'Add New BOM' }}
                        </h3>
                        
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 font-sans">
                            <!-- Product Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Finished Product</label>
                                <select wire:model="product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    <option value="">Select Finished Product</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                                @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- BOM Version -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Version</label>
                                <input type="text" wire:model="bom_version" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                @error('bom_version') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Notes / Details</label>
                                <input type="text" wire:model="notes" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. Standard production formula">
                            </div>
                        </div>

                        <!-- BOM Items / Components -->
                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-md font-medium text-gray-900 font-display">Required Materials / Components</h4>
                                <button type="button" wire:click="addItem" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 font-display font-semibold">
                                    + Add Material
                                </button>
                            </div>

                            <div class="space-y-4 font-sans">
                                @foreach($items as $index => $item)
                                    <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                                        <!-- Material Selection -->
                                        <div class="md:col-span-6">
                                            <label class="block text-xs text-gray-500">Raw Material / Component</label>
                                            <select wire:model="items.{{ $index }}.component_id" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm bg-white">
                                                <option value="">Select Material</option>
                                                @foreach($products as $p)
                                                    <option value="{{ $p->id }}">{{ $p->name }} (Current Stock: {{ $p->stock }})</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Quantity Required -->
                                        <div class="md:col-span-3">
                                            <label class="block text-xs text-gray-500">Qty Required</label>
                                            <input type="number" step="0.001" wire:model="items.{{ $index }}.qty" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm">
                                        </div>

                                        <!-- Unit -->
                                        <div class="md:col-span-2">
                                            <label class="block text-xs text-gray-500">Unit</label>
                                            <input type="text" wire:model="items.{{ $index }}.unit" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3 text-sm" placeholder="pcs, kg, gr">
                                        </div>

                                        <!-- Actions -->
                                        <div class="md:col-span-1 text-center mt-4">
                                            <button type="button" wire:click="removeItem({{ $index }})" class="text-red-600 hover:text-red-900 text-sm">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                            Save BOM Recipe
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
