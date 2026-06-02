<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900 tracking-tight">Project Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage projects, tasks, and team assignments.</p>
        </div>
        <div class="flex gap-2">
            <button wire:click="createTask" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                + Task
            </button>
            <button wire:click="createProject" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                + Project
            </button>
        </div>
    </div>

    @if(session()->has('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-400 text-emerald-700 rounded shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="switchTab('projects')" class="{{ $activeTab === 'projects' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Projects
            </button>
            <button wire:click="switchTab('tasks')" class="{{ $activeTab === 'tasks' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                Tasks Kanban
            </button>
        </nav>
    </div>

    <!-- Projects Tab -->
    @if($activeTab === 'projects')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($projects as $project)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition">
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $project->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ optional($project->customer)->name ?? 'Internal Project' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold
                                @if($project->status === 'planned') bg-gray-100 text-gray-800
                                @elseif($project->status === 'in_progress') bg-blue-100 text-blue-800
                                @elseif($project->status === 'completed') bg-emerald-100 text-emerald-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="p-5 bg-gray-50 flex justify-between items-center text-sm">
                        <span class="text-gray-600 text-xs">
                            {{ $project->start_date ? $project->start_date->format('M d, Y') : 'No date' }} 
                            - 
                            {{ $project->end_date ? $project->end_date->format('M d, Y') : 'No date' }}
                        </span>
                        <div class="flex gap-2">
                            <button wire:click="editProject({{ $project->id }})" class="text-blue-600 hover:text-blue-800 font-medium text-xs">Edit</button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-400 italic bg-white rounded border border-dashed border-gray-200">
                    No projects found.
                </div>
            @endforelse
        </div>
    @endif

    <!-- Tasks Tab (Kanban) -->
    @if($activeTab === 'tasks')
        <div class="flex gap-6 overflow-x-auto pb-4">
            @php $stages = ['todo', 'in_progress', 'done']; @endphp
            @foreach($stages as $stage)
                <div class="flex-shrink-0 w-80 bg-gray-50 rounded-lg border border-gray-200 p-4"
                     ondragover="event.preventDefault()"
                     ondragenter="event.preventDefault()"
                     ondrop="event.preventDefault(); @this.call('updateTaskStatus', event.dataTransfer.getData('text/plain'), '{{ $stage }}')">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-gray-800 uppercase tracking-wider text-xs">{{ str_replace('_', ' ', $stage) }}</h3>
                        <span class="bg-gray-200 text-gray-700 text-xs py-0.5 px-2 rounded-full font-bold">
                            {{ $tasks->where('status', $stage)->count() }}
                        </span>
                    </div>
                    <div class="space-y-3 min-h-[150px]">
                        @foreach($tasks->where('status', $stage) as $task)
                            <div class="bg-white p-4 rounded shadow-sm border border-gray-200 hover:shadow-md cursor-grab active:cursor-grabbing group transition duration-150 hover:border-blue-400"
                                 draggable="true"
                                 ondragstart="event.dataTransfer.setData('text/plain', '{{ $task->id }}')">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xxs font-bold px-1.5 py-0.5 rounded
                                        @if($task->priority === 'high') bg-red-100 text-red-800
                                        @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ ucfirst($task->priority) }}
                                    </span>
                                    <span class="text-xxs text-gray-400">{{ optional($task->project)->name }}</span>
                                </div>
                                <h4 class="font-bold text-gray-900 text-sm mb-1 leading-snug">{{ $task->name }}</h4>
                                <div class="flex justify-between items-center mt-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-center text-xs text-gray-500">
                                        {{ optional($task->assignee)->name ?? 'Unassigned' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Project Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4">{{ $isEditMode ? 'Edit Project' : 'Create Project' }}</h3>
                    <form wire:submit.prevent="storeProject" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Project Name</label>
                            <input wire:model="name" type="text" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Customer</label>
                            <select wire:model="customer_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                <option value="">-- Internal --</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input wire:model="start_date" type="date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input wire:model="end_date" type="date" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select wire:model="status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                <option value="planned">Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="on_hold">On Hold</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:flow-row-dense">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 sm:col-start-2 sm:text-sm">Save</button>
                            <button type="button" wire:click="$set('isOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:col-start-1 sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Task Modal -->
    @if($isTaskOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left shadow-xl transform transition-all sm:my-8 sm:max-w-lg sm:w-full sm:p-6">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 mb-4">{{ $isEditMode ? 'Edit Task' : 'Create Task' }}</h3>
                    <form wire:submit.prevent="storeTask" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Project</label>
                            <select wire:model="selected_project_id" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                <option value="">-- Select Project --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Task Name</label>
                            <input wire:model="task_name" type="text" class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assign To</label>
                                <select wire:model="assigned_to" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                    <option value="">-- Unassigned --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status</label>
                                <select wire:model="task_status" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm sm:text-sm">
                                    <option value="todo">To Do</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="done">Done</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:flow-row-dense">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-white hover:bg-blue-700 sm:col-start-2 sm:text-sm">Save Task</button>
                            <button type="button" wire:click="$set('isTaskOpen', false)" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 sm:mt-0 sm:col-start-1 sm:text-sm">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
