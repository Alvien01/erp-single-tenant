<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Quality Control (QC)</h1>
            <p class="text-sm text-gray-500 mt-1">Establish quality test checkpoints and track status checks for received or produced goods.</p>
        </div>
        @if($activeTab === 'checkpoints')
            <button wire:click="createCheckpoint" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Checkpoint
            </button>
        @endif
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="$set('activeTab', 'checkpoints')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'checkpoints' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                QC Checkpoints Setup
            </button>
            <button wire:click="$set('activeTab', 'checks')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'checks' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Quality Checks Log
            </button>
        </nav>
    </div>

    @if($activeTab === 'checkpoints')
        <!-- Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search checkpoints by product or test name...">
            </div>
        </div>

        <!-- Checkpoints Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Product</th>
                            <th class="py-3.5 px-6">Test Name</th>
                            <th class="py-3.5 px-6">Acceptance Criteria</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($checkpoints as $cp)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 text-gray-900 font-semibold">{{ $cp->product->name }}</td>
                                <td class="py-4 px-6 font-medium text-blue-600">{{ $cp->test_name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $cp->criteria }}</td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <button wire:click="recordCheck({{ $cp->id }})" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer font-bold">Run Check</button>
                                    <button wire:click="editCheckpoint({{ $cp->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteCheckpoint({{ $cp->id }})" wire:confirm="Delete this checkpoint?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">
                                    No quality checkpoints setup.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($checkpoints->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $checkpoints->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'checks')
        <!-- QC Checks Logs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Timestamp</th>
                            <th class="py-3.5 px-6">Checkpoint Test</th>
                            <th class="py-3.5 px-6">Target Reference</th>
                            <th class="py-3.5 px-6">Inspector</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6">Notes</th>
                            <th class="py-3.5 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($checks as $check)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 text-gray-500 font-mono">{{ $check->checked_at->format('Y-m-d H:i') }}</td>
                                <td class="py-4 px-6 text-gray-900 font-semibold">
                                    {{ $check->checkpoint->product->name }} - <span class="text-blue-600">{{ $check->checkpoint->test_name }}</span>
                                </td>
                                <td class="py-4 px-6 text-gray-700 font-medium">
                                    {{ $check->reference_type }} #{{ $check->reference_id }}
                                </td>
                                <td class="py-4 px-6 text-gray-600">{{ $check->checker->name }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ $check->status === 'passed' ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}
                                    ">
                                        {{ strtoupper($check->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-gray-500">{{ $check->notes ?: '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <button wire:click="deleteCheck({{ $check->id }})" wire:confirm="Delete this log?" class="text-red-600 hover:text-red-900 cursor-pointer font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-500">
                                    No quality tests recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($checks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $checks->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modals Section -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    @if($modalType === 'checkpoint')
                        <!-- Checkpoint Setup Modal -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                {{ $checkpoint_id ? 'Edit Quality Checkpoint' : 'Create Quality Checkpoint' }}
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Target Product</label>
                                    <select wire:model="product_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">Select Product</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}">{{ $p->name }} ({{ $p->code }})</option>
                                        @endforeach
                                    </select>
                                    @error('product_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Test name</label>
                                    <input type="text" wire:model="test_name" placeholder="e.g. Dimensions verification, Paint inspection" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('test_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Acceptance Criteria Description</label>
                                    <textarea wire:model="criteria" placeholder="Explain the exact metric or quality look required..." class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" rows="3"></textarea>
                                    @error('criteria') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveCheckpoint" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Checkpoint
                            </button>
                        </div>

                    @elseif($modalType === 'check')
                        <!-- Record check run -->
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Record QC Quality Check</h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference Type</label>
                                        <select wire:model="reference_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                            <option value="GoodReceipt">GoodReceipt</option>
                                            <option value="ProductionOrder">ProductionOrder</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Reference ID</label>
                                        <input type="number" wire:model="reference_id" placeholder="e.g. 5" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('reference_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Check Status</label>
                                    <select wire:model="status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="passed">PASSED</option>
                                        <option value="failed">FAILED</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Notes / Inspection findings</label>
                                    <textarea wire:model="notes" placeholder="Optionally add notes..." class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveCheck" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-emerald-600 border border-transparent rounded-md hover:bg-emerald-700 focus:outline-none font-display font-semibold">
                                Submit Log Check
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
