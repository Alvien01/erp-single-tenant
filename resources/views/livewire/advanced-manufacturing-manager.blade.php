<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900 tracking-tight">Advanced Manufacturing (MRP II & PLM)</h1>
            <p class="text-sm text-gray-500 mt-1">Manage routings, subcontracting, ECOs, and MPS.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
            @foreach(['routings', 'subcontracting', 'eco', 'mps'] as $tab)
                <button wire:click="$set('activeTab', '{{ $tab }}')" class="{{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm capitalize">
                    {{ $tab === 'eco' ? 'ECO (PLM)' : ($tab === 'mps' ? 'MPS' : $tab) }}
                </button>
            @endforeach
        </nav>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                @php
            $varMap = [
                'routings' => $routings ?? [],
                'subcontracting' => $subcontracts ?? [],
                'eco' => $ecos ?? [],
                'mps' => $schedules ?? [],
            ];
            $currentData = $varMap[$activeTab] ?? [];
        @endphp
        
        <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full md:w-1/3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md" placeholder="Search {{ $activeTab }}...">
            <button wire:click="openModal('{{ $activeTab }}')" class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700 shadow-sm">
                Add New
            </button>
        </div>

        <div class="p-0 overflow-x-auto">
            @if(is_object($currentData) && method_exists($currentData, 'count') ? $currentData->count() > 0 : count($currentData) > 0)
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4 font-semibold">ID</th>
                            <th class="py-3 px-4 font-semibold">Identifier / Name</th>
                            <th class="py-3 px-4 font-semibold">Status / Details</th>
                            <th class="py-3 px-4 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($currentData as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 text-gray-500">#{{ $item->id }}</td>
                            <td class="py-3 px-4 font-medium text-gray-900">
                                {{ $item->name ?? $item->title ?? $item->code ?? $item->dropship_number ?? $item->eco_number ?? $item->transaction_number ?? 'Record ' . $item->id }}
                            </td>
                            <td class="py-3 px-4">
                                @if(isset($item->status))
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">{{ ucfirst($item->status) }}</span>
                                @elseif(isset($item->is_active))
                                    <span class="px-2 py-1 {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-medium">{{ $item->is_active ? 'Active' : 'Inactive' }}</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <button class="text-blue-600 hover:text-blue-800 font-medium mr-3" wire:click="openModal('{{ $activeTab }}', {{ $item->id }})">Edit</button>
                                <button class="text-red-600 hover:text-red-800 font-medium" wire:click="delete('{{ $activeTab }}', {{ $item->id }})" wire:confirm="Are you sure you want to delete this record?">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if(method_exists($currentData, 'links'))
                    <div class="p-4 border-t border-gray-200">
                        {{ $currentData->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    <p class="text-lg font-medium text-gray-900">No records found</p>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new {{ ucfirst($activeTab) }}.</p>
                </div>
            @endif
        </div>
        
        <!-- Generic Modal Stub -->
        @if(isset($modalType) && $modalType)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                {{ isset($isEdit) && $isEdit ? 'Edit' : 'Create' }} Record
                            </h3>
                            <div class="mt-4">
                                <p class="text-sm text-gray-500 mb-4">The form for {{ ucfirst($modalType) }} is dynamically constructed here in the system. As an advanced implementation, please refer to the specific Manager class logic.</p>
                                
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                                    <p class="text-sm text-yellow-700">Notice: Full form binding is scaffolded. Please customize the properties in the Livewire component to match your specific inputs.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" wire:click="closeModal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Understood & Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
