import os
import re

BLADE_DIR = 'resources/views/livewire'
LIVEWIRE_DIR = 'app/Livewire'

managers = [
    'multi-company',
    'advanced-logistics',
    'advanced-manufacturing',
    'advanced-accounting',
    'website-cms',
    'marketing-automation',
    'fsm'
]

# A generic snippet to replace the stub in blades
GENERIC_TABLE = """
        <div class="p-0 overflow-x-auto">
            @if(count($$VAR) > 0)
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500">
                        <tr>
                            <th class="py-3 px-4">ID</th>
                            <th class="py-3 px-4">Details</th>
                            <th class="py-3 px-4">Created At</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($$VAR as $item)
                        <tr>
                            <td class="py-3 px-4">{{ $item->id }}</td>
                            <td class="py-3 px-4">
                                {{ $item->name ?? $item->title ?? $item->code ?? $item->description ?? 'Record #' . $item->id }}
                            </td>
                            <td class="py-3 px-4">{{ $item->created_at?->format('Y-m-d') }}</td>
                            <td class="py-3 px-4 text-right">
                                <button class="text-blue-600 hover:underline mr-2" wire:click="openModal('{{ $activeTab }}', {{ $item->id }})">Edit</button>
                                <button class="text-red-600 hover:underline" wire:click="delete('{{ $activeTab }}', {{ $item->id }})" wire:confirm="Are you sure?">Delete</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4">
                    {{ $$VAR->links() ?? '' }}
                </div>
            @else
                <div class="p-6 text-center text-gray-500">
                    <p>No records found for {{ ucfirst($activeTab) }}.</p>
                </div>
            @endif
        </div>
"""

for manager in managers:
    blade_file = os.path.join(BLADE_DIR, f"{manager}-manager.blade.php")
    if not os.path.exists(blade_file): continue
    
    with open(blade_file, 'r') as f:
        content = f.read()

    # Find the variables passed to the view
    php_file = os.path.join(LIVEWIRE_DIR, f"{manager.title().replace('-', '')}Manager.php")
    
    # We will just replace the stub with a generic table that tries to guess the variable.
    # In blade, we can use a dynamic variable mapping or just an if/else based on activeTab.
    
    # Let's extract the render array keys from the PHP file to map tabs to variables.
    mapping_code = """
        @php
            $varMap = [
"""
    # Quick static mapping based on what we know:
    if manager == 'multi-company':
        mapping_code += "                'companies' => $companies ?? [],\n                'rules' => $rules ?? [],\n"
    elif manager == 'advanced-logistics':
        mapping_code += "                'carriers' => $carriers ?? [],\n                'dropship' => $dropships ?? [],\n"
    elif manager == 'advanced-manufacturing':
        mapping_code += "                'routings' => $routings ?? [],\n                'subcontracting' => $subcontracts ?? [],\n                'eco' => $ecos ?? [],\n                'mps' => $schedules ?? [],\n"
    elif manager == 'advanced-accounting':
        mapping_code += "                'deferred' => $deferredEntries ?? [],\n                'providers' => $providers ?? [],\n                'transactions' => $transactions ?? [],\n"
    elif manager == 'website-cms':
        mapping_code += "                'pages' => $pages ?? [],\n                'blog' => $blogs ?? [],\n                'appointments' => $appointments ?? [],\n                'events' => $events ?? [],\n                'elearning' => $courses ?? [],\n"
    elif manager == 'marketing-automation':
        mapping_code += "                'workflows' => $workflows ?? [],\n                'surveys' => $surveys ?? [],\n                'responses' => $responses ?? [],\n"
    elif manager == 'fsm':
        mapping_code += "                'orders' => $orders ?? [],\n                'worksheets' => $worksheets ?? [],\n                'parts' => $parts ?? [],\n"

    mapping_code += """            ];
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
        @if($modalType)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                {{ $isEdit ? 'Edit' : 'Create' }} Record
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
"""

    # Replace the stub
    stub_pattern = re.compile(r'<div class="p-4 border-b border-gray-200">.*?</div>', re.DOTALL)
    
    new_content = stub_pattern.sub(mapping_code, content)
    
    if new_content != content:
        with open(blade_file, 'w') as f:
            f.write(new_content)
        print(f"Updated {blade_file}")

print("Done generating blades!")
