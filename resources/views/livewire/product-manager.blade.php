<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Product Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage your ERP inventory catalog, details, prices, and stock limits.</p>
        </div>
        <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 transition ease-in-out duration-150">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Product
        </button>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex-1 max-w-md relative">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by name or code...">
        </div>
        
        <div class="w-full md:w-64">
            <select wire:model.live="category_id" class="w-full py-2 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 bg-white">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                        <th class="py-3.5 px-6">Product Code</th>
                        <th class="py-3.5 px-6">Image</th>
                        <th class="py-3.5 px-6">Name</th>
                        <th class="py-3.5 px-6">Category</th>
                        <th class="py-3.5 px-6 text-center">Type</th>
                        <th class="py-3.5 px-6 text-right">Price</th>
                        <th class="py-3.5 px-6 text-right">Stock</th>
                        <th class="py-3.5 px-6 text-right">Min Stock</th>
                        <th class="py-3.5 px-6 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($products as $product)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="py-4 px-6 font-mono font-medium text-blue-600">{{ $product->code }}</td>
                            <td class="py-4 px-6">
                                @if($product->image && isset($product->image['sm']))
                                    <img src="{{ Storage::url($product->image['sm']) }}" class="h-10 w-10 object-cover rounded shadow-sm border border-gray-200" alt="{{ $product->name }}">
                                @else
                                    <div class="h-10 w-10 rounded bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 font-medium text-gray-900">{{ $product->name }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ $product->category->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                @if(($product->type ?? 'ready_stock') === 'ready_stock')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        Ready Stock
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                        Pre-Order
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                            <td class="py-4 px-6 text-right font-mono">
                                @if(($product->type ?? 'ready_stock') === 'ready_stock')
                                    <span class="{{ $product->stock <= $product->min_stock ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                        {{ number_format($product->stock, 0, ',', '.') }} {{ $product->unit }}
                                    </span>
                                @else
                                    <span class="text-gray-400 italic">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-gray-500">
                                @if(($product->type ?? 'ready_stock') === 'ready_stock')
                                    {{ number_format($product->min_stock, 0, ',', '.') }} {{ $product->unit }}
                                @else
                                    <span class="text-gray-400 italic">—</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center space-x-2">
                                <button wire:click="edit({{ $product->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                <button wire:click="confirmDelete({{ $product->id }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-gray-500">
                                No products found match your search criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($products->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $products->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form (Create / Edit) -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Content -->
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            {{ $isEditMode ? 'Edit Product' : 'Add New Product' }}
                        </h3>
                        <div class="mt-4 space-y-4">
                            <!-- Product Code -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Code</label>
                                <input type="text" wire:model="code" readonly class="mt-1 block w-full border border-gray-300 bg-gray-50 text-gray-500 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm cursor-not-allowed">
                                @error('code') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Product Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Name</label>
                                <input type="text" wire:model="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="selected_category_id" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('selected_category_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <!-- Price -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Price (IDR)</label>
                                    <input type="number" wire:model="price" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('price') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Unit -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Unit</label>
                                    <input type="text" wire:model="unit" placeholder="pcs, box, kg..." class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('unit') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Product Type Toggle -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Type</label>
                                <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                                    <label class="flex-1 relative cursor-pointer">
                                        <input type="radio" wire:model.live="type" value="ready_stock" class="sr-only peer">
                                        <div class="flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-medium text-center transition-all duration-200
                                            peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:border-emerald-200 peer-checked:shadow-inner
                                            text-gray-500 hover:bg-gray-50">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                            Ready Stock
                                        </div>
                                    </label>
                                    <div class="w-px bg-gray-300"></div>
                                    <label class="flex-1 relative cursor-pointer">
                                        <input type="radio" wire:model.live="type" value="pre_order" class="sr-only peer">
                                        <div class="flex items-center justify-center gap-2 py-2.5 px-4 text-sm font-medium text-center transition-all duration-200
                                            peer-checked:bg-amber-50 peer-checked:text-amber-700 peer-checked:border-amber-200 peer-checked:shadow-inner
                                            text-gray-500 hover:bg-gray-50">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.828a1 1 0 101.415-1.414L11 9.586V6z" clip-rule="evenodd"></path></svg>
                                            PO (Pre-Order)
                                        </div>
                                    </label>
                                </div>
                                @error('type') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            @if($type === 'ready_stock')
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Stock -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Current Stock</label>
                                    <input type="number" wire:model="stock" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Min Stock -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Min. Alert Stock</label>
                                    <input type="number" wire:model="min_stock" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('min_stock') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @else
                            <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-2">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                                <span>Pre-Order products do not require stock tracking. Stock and minimum alert stock will be set to 0.</span>
                            </div>
                            @endif

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Product Image (Optional)</label>
                                <input type="file" wire:model="image" accept="image/*" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                <div wire:loading wire:target="image" class="text-sm text-blue-500 mt-1">Uploading...</div>
                                @error('image') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                
                                <!-- Image Preview -->
                                @if ($image)
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-1">New image preview:</p>
                                        <img src="{{ $image->temporaryUrl() }}" class="h-24 w-24 object-cover rounded border border-gray-200">
                                    </div>
                                @elseif($isEditMode && !empty($existing_images) && isset($existing_images['md']))
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-500 mb-1">Current image:</p>
                                        <img src="{{ Storage::url($existing_images['md']) }}" class="h-24 w-24 object-cover rounded border border-gray-200">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                            Cancel
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal Alert -->
    @if($confirmingProductDeletion)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-delete-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop with blur -->
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" wire:click="cancelDelete"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Modal Alert Content -->
                <div class="inline-block align-bottom bg-white rounded-2xl px-4 pt-5 pb-4 text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 border border-gray-100">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Alert Triangle Icon -->
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                            <h3 class="text-lg leading-6 font-semibold text-gray-900" id="modal-delete-title">
                                Delete Product
                            </h3>
                            <div class="mt-2 text-sm text-gray-500">
                                <p>Are you sure you want to delete this product? This action cannot be undone.</p>
                                @if($deleteProductName)
                                    <div class="mt-3 p-3 bg-red-50/70 border border-red-100 rounded-lg text-left">
                                        <div class="text-xs font-mono font-semibold text-blue-600">{{ $deleteProductCode }}</div>
                                        <div class="text-sm font-semibold text-gray-900 mt-0.5">{{ $deleteProductName }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 sm:mt-5 sm:flex sm:flex-row-reverse gap-2">
                        <button type="button" wire:click="delete" wire:loading.attr="disabled" class="w-full inline-flex justify-center items-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-red-600 text-sm font-medium text-white hover:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto transition-colors disabled:opacity-50 cursor-pointer">
                            <span wire:loading.remove wire:target="delete">Delete</span>
                            <span wire:loading wire:target="delete" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Deleting...
                            </span>
                        </button>
                        <button type="button" wire:click="cancelDelete" class="mt-2 sm:mt-0 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-300 sm:w-auto transition-colors cursor-pointer">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
