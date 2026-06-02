<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Workflow Approvals Engine</h1>
            <p class="text-sm text-gray-500 mt-1">Configure automated multi-level approval hierarchies for purchases, sales, transfers, and budgets.</p>
        </div>
        @if($activeTab === 'rules')
            <button wire:click="createRule" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create Rule
            </button>
        @endif
    </div>

    <!-- Toast / Feedback Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'requests')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'requests' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Pending Requests Ledger
            </button>
            <button wire:click="$set('activeTab', 'rules')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'rules' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Hierarchy Rules Config
            </button>
        </nav>
    </div>

    @if($activeTab === 'requests')
        <!-- Requests List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Request ID</th>
                            <th class="py-3.5 px-6">Doc Type</th>
                            <th class="py-3.5 px-6">Reference ID</th>
                            <th class="py-3.5 px-6">Requested By</th>
                            <th class="py-3.5 px-6">Role Req.</th>
                            <th class="py-3.5 px-6">Approver Decision</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($requests as $req)
                            @php
                                $statusColors = match($req->status) {
                                    'pending' => 'bg-amber-100 text-amber-800',
                                    'approved' => 'bg-emerald-100 text-emerald-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono font-bold text-gray-900">#{{ $req->id }}</td>
                                <td class="py-4 px-6 font-medium text-gray-700">{{ $req->rule->module_type }}</td>
                                <td class="py-4 px-6 font-mono text-blue-600">ID: {{ $req->reference_id }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $req->requester->name ?? 'System' }}</td>
                                <td class="py-4 px-6 text-gray-500 font-semibold uppercase">{{ $req->rule->role_required }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $req->approver->name ?? '-' }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors }}">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($req->status === 'pending')
                                        <button wire:click="openDecision({{ $req->id }})" class="text-blue-600 hover:text-blue-900 font-display font-medium text-xs cursor-pointer">Submit Decision</button>
                                    @else
                                        <span class="text-gray-400 italic text-xs">Evaluated</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-400 italic">No approval requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($requests->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $requests->links() }}
                </div>
            @endif
        </div>
    @else
        <!-- Rules Config List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Document Type</th>
                            <th class="py-3.5 px-6 text-right">Min Amount</th>
                            <th class="py-3.5 px-6 text-right">Max Amount</th>
                            <th class="py-3.5 px-6">Approver Role</th>
                            <th class="py-3.5 px-6 text-center">Stage Sequence</th>
                            <th class="py-3.5 px-6">Active</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $rule->module_type }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($rule->min_amount, 0) }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($rule->max_amount, 0) }}</td>
                                <td class="py-4 px-6 text-gray-500 font-semibold uppercase">{{ $rule->role_required }}</td>
                                <td class="py-4 px-6 text-center font-bold text-blue-600 font-mono">{{ $rule->sequence }}</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold {{ $rule->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-red-800' }}">
                                        {{ $rule->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center space-x-3 font-display font-medium text-xs">
                                    <button wire:click="editRule({{ $rule->id }})" class="text-blue-600 hover:text-blue-900 cursor-pointer">Edit</button>
                                    <button wire:click="deleteRule({{ $rule->id }})" wire:confirm="Delete this hierarchy stage rule?" class="text-red-600 hover:text-red-900 cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-gray-400 italic">No rules configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rules->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $rules->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Decision Modal -->
    @if($isOpenDecisionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeDecision"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeDecision" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">Evaluate Request Decision</h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Approval Verdict</label>
                                <select wire:model="decision_status" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="approved">Approve & Release</option>
                                    <option value="rejected">Reject & Cancel</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Decision Commentary / Reason</label>
                                <textarea wire:model="decision_notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3" placeholder="Provide reason or context for audit trail..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeDecision" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="storeDecision" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Submit Verdict
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Rules Config Modal -->
    @if($isOpenRuleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeRuleModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full sm:p-6 font-sans">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeRuleModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $isEditRuleMode ? 'Edit Approval Rule' : 'Create Approval Rule' }}
                        </h3>
                        <div class="mt-4 space-y-4 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Document Class Type</label>
                                <select wire:model="document_type" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="Purchase">Purchase Order</option>
                                    <option value="Sale">Sales Order</option>
                                    <option value="Transfer">Warehouse Transfer</option>
                                    <option value="Budget">Budget Limit</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Min Value Limit</label>
                                    <input type="number" wire:model="min_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Max Value Limit</label>
                                    <input type="number" wire:model="max_amount" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase">Required Role Level</label>
                                <select wire:model="approver_role" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                    <option value="manager">Manager</option>
                                    <option value="finance">Finance / Comptroller</option>
                                    <option value="admin">System Administrator</option>
                                </select>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Stage Sequence</label>
                                    <input type="number" wire:model="sequence" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase">Rule Active</label>
                                    <select wire:model="is_active" class="mt-1 block w-full border border-gray-300 rounded-md py-1.5 px-3">
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="closeRuleModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                            Cancel
                        </button>
                        <button type="button" wire:click="storeRule" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                            Save Rule
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
