<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900 tracking-tight">Field Service Management (FSM)</h1>
            <p class="text-sm text-gray-500 mt-1">Manage field technicians, onsite tasks, and digital worksheets.</p>
        </div>
        <button class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
            Create FSM Order
        </button>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <input wire:model.live.debounce.300ms="search" type="text" class="w-full md:w-1/3 focus:ring-blue-500 focus:border-blue-500 sm:text-sm border-gray-300 rounded-md" placeholder="Search orders...">
        </div>

        <div class="p-6 text-center text-gray-500">
            <p>FSM Orders Map & Data table will be displayed here.</p>
        </div>
    </div>
</div>
