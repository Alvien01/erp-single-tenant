<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Recruitment & ATS</h1>
            <p class="text-sm text-gray-500 mt-1">Manage job openings and evaluate applicant pipelines interactively.</p>
        </div>
        <div class="flex space-x-2">
            @if($activeTab === 'applicants')
                <button wire:click="openModal('applicant')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                    Add Applicant
                </button>
            @elseif($activeTab === 'jobs')
                <button wire:click="openModal('job')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                    Create Job Position
                </button>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if (session()->has('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabs and Filter Toolbar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <!-- Tabs -->
            <div class="flex space-x-2 border-b border-gray-100">
                <button wire:click="$set('activeTab', 'applicants')" class="pb-3 px-4 font-semibold text-sm transition {{ $activeTab === 'applicants' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Applicants
                </button>
                <button wire:click="$set('activeTab', 'jobs')" class="pb-3 px-4 font-semibold text-sm transition {{ $activeTab === 'jobs' ? 'text-blue-600 border-b-2 border-blue-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Job Positions
                </button>
            </div>

            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                @if($activeTab === 'applicants')
                    <select wire:model.live="selectedJobId" class="w-full sm:w-48 py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
                        <option value="">All Job Openings</option>
                        @foreach($jobs as $j)
                            <option value="{{ $j->id }}">{{ $j->title }}</option>
                        @endforeach
                    </select>
                @endif
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
                </div>
            </div>
        </div>
    </div>

    <!-- Active Tab Display -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        @if($activeTab === 'applicants')
            <!-- Applicants Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">Applicant</th>
                            <th class="py-3.5 px-6 font-semibold">Job Opening</th>
                            <th class="py-3.5 px-6 font-semibold">Applied Date</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Pipeline Stage</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($applicantsList as $app)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900">{{ $app->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $app->email }} | {{ $app->phone ?: '-' }}</div>
                                </td>
                                <td class="py-4 px-6 text-gray-700 font-semibold">{{ $app->jobPosition->title }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $app->applied_date->format('d M Y') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <div class="inline-flex space-x-1">
                                        @foreach(['applied', 'interview', 'offered', 'hired', 'rejected'] as $stage)
                                            @php
                                                $active = $app->status === $stage;
                                                $btnColor = match($stage) {
                                                    'applied' => $active ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100',
                                                    'interview' => $active ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100',
                                                    'offered' => $active ? 'bg-purple-600 text-white' : 'bg-purple-50 text-purple-700 hover:bg-purple-100',
                                                    'hired' => $active ? 'bg-emerald-600 text-white' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100',
                                                    'rejected' => $active ? 'bg-red-600 text-white' : 'bg-red-50 text-red-700 hover:bg-red-100',
                                                };
                                            @endphp
                                            <button wire:click="updateApplicantStatus({{ $app->id }}, '{{ $stage }}')" class="px-2 py-0.5 text-xs font-semibold rounded {{ $btnColor }} transition border border-transparent">
                                                {{ ucfirst($stage) }}
                                            </button>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button wire:click="openModal('applicant', {{ $app->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this applicant?') || event.stopImmediatePropagation()" wire:click="deleteApplicant({{ $app->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">No applicants found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $applicantsList->links() }}
            </div>

        @elseif($activeTab === 'jobs')
            <!-- Job Positions Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-3.5 px-6 font-semibold">Job Title</th>
                            <th class="py-3.5 px-6 font-semibold">Department</th>
                            <th class="py-3.5 px-6 font-semibold font-mono">Expected Employees</th>
                            <th class="py-3.5 px-6 font-semibold font-mono">Total Candidates</th>
                            <th class="py-3.5 px-6 font-semibold text-center">Status</th>
                            <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($jobsList as $job)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ $job->title }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $job->department ?: '-' }}</td>
                                <td class="py-4 px-6 text-gray-600 font-mono">{{ $job->expected_employees }}</td>
                                <td class="py-4 px-6 text-gray-600 font-mono">{{ $job->applicants_count }} applicants</td>
                                <td class="py-4 px-6 text-center">
                                    @php
                                        $badgeColor = match($job->status) {
                                            'open' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                            'closed' => 'bg-red-50 text-red-700 border-red-100',
                                        };
                                    @endphp
                                    <span class="px-2.5 py-0.5 inline-flex items-center text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                        {{ ucfirst($job->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-2">
                                    <button wire:click="openModal('job', {{ $job->id }})" class="text-blue-600 hover:text-blue-900 font-medium">Edit</button>
                                    <button onclick="confirm('Are you sure you want to delete this job position?') || event.stopImmediatePropagation()" wire:click="deleteJob({{ $job->id }})" class="text-red-600 hover:text-red-900 font-medium">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-500">No job openings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $jobsList->links() }}
            </div>
        @endif
    </div>

    <!-- Modals -->
    @if($modalType)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    @if($modalType === 'job')
                        <!-- Job Position Form Modal -->
                        <form wire:submit.prevent="saveJob" class="space-y-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                {{ $isEdit ? 'Edit Job Opening' : 'Create Job Opening' }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Job Title</label>
                                    <input wire:model="job_title" type="text" placeholder="e.g. Senior PHP Developer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('job_title') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Department</label>
                                    <input wire:model="job_department" type="text" placeholder="e.g. IT Department" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('job_department') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Expected Employees</label>
                                    <input wire:model="job_expected_employees" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('job_expected_employees') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Status</label>
                                    <select wire:model="job_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        <option value="open">Open</option>
                                        <option value="closed">Closed</option>
                                    </select>
                                    @error('job_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Job Description</label>
                                    <textarea wire:model="job_description" rows="3" placeholder="Job details, requirements, etc..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm"></textarea>
                                    @error('job_description') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                            </div>
                        </form>

                    @elseif($modalType === 'applicant')
                        <!-- Applicant Form Modal -->
                        <form wire:submit.prevent="saveApplicant" class="space-y-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                {{ $isEdit ? 'Edit Applicant Details' : 'Add New Applicant' }}
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Full Name</label>
                                    <input wire:model="applicant_name" type="text" placeholder="John Doe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('applicant_name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Email Address</label>
                                    <input wire:model="applicant_email" type="email" placeholder="john@example.com" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('applicant_email') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Phone Number</label>
                                    <input wire:model="applicant_phone" type="text" placeholder="e.g. 08123456789" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                    @error('applicant_phone') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Job Opening</label>
                                    <select wire:model="applicant_job_position_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                        <option value="">Select Job Opening</option>
                                        @foreach($jobs as $j)
                                            <option value="{{ $j->id }}">{{ $j->title }}</option>
                                        @endforeach
                                    </select>
                                    @error('applicant_job_position_id') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Applied Date</label>
                                    <input wire:model="applicant_applied_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    @error('applicant_applied_date') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Initial Pipeline Stage</label>
                                    <select wire:model="applicant_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                                        <option value="applied">Applied</option>
                                        <option value="interview">Interview</option>
                                        <option value="offered">Offered</option>
                                        <option value="hired">Hired</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                    @error('applicant_status') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Internal Evaluation Notes</label>
                                    <textarea wire:model="applicant_notes" rows="3" placeholder="Evaluation notes, interview feedback..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm"></textarea>
                                    @error('applicant_notes') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Save</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
