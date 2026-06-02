<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900 tracking-tight">Marketing Automation</h1>
            <p class="text-sm text-gray-500 mt-1">Visual workflows, dynamic surveys, and automated triggers.</p>
        </div>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'workflows')" class="{{ $activeTab === 'workflows' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Workflows
            </button>
            <button wire:click="$set('activeTab', 'surveys')" class="{{ $activeTab === 'surveys' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Surveys & Forms
            </button>
        </nav>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 text-center text-gray-500">
            <p>Data table for {{ ucfirst($activeTab) }} will be displayed here.</p>
        </div>
    </div>
</div>
