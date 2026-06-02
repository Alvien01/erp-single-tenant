<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Document Management & E-Sign</h1>
            <p class="text-sm text-gray-500 mt-1">Store files, manage digital version histories, and execute secured digital signatures.</p>
        </div>
        <div>
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150">
                Upload Document
            </button>
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

    <!-- Statistics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Document Archive</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Digitally Signed & Locked</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['signed'] }}</p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.952 11.952 0 01-6.83 1.666L5 4.847V10.5c0 5.308 3.82 9.802 8.783 10.796A11.954 11.954 0 0021 12.5V4.847l-.382-.031z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Execution / Signs</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900 font-mono">{{ $stats['pending'] }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
            <div class="relative w-full sm:w-80">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search archive name..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
            </div>

            <div class="flex gap-2 w-full sm:w-auto">
                <select wire:model.live="category" class="w-full sm:w-40 py-2 px-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 text-sm">
                    <option value="">All Categories</option>
                    <option value="General">General</option>
                    <option value="Contract">Contract</option>
                    <option value="Invoice">Invoice</option>
                    <option value="HRD">HRD / Personnel</option>
                </select>

                <select wire:model.live="status" class="w-full sm:w-40 py-2 px-3 border border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500 text-sm">
                    <option value="">All Sign Status</option>
                    <option value="draft">Draft / General</option>
                    <option value="pending_signature">Pending Sign</option>
                    <option value="signed">Signed & Secured</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Documents Archive Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6 font-semibold">Document Details</th>
                        <th class="py-3.5 px-6 font-semibold">Category</th>
                        <th class="py-3.5 px-6 font-semibold font-mono text-center">Version</th>
                        <th class="py-3.5 px-6 font-semibold">Author</th>
                        <th class="py-3.5 px-6 font-semibold text-center">Execution Status</th>
                        <th class="py-3.5 px-6 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="py-4 px-6">
                                <div class="font-bold text-gray-900 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $doc->name }}
                                </div>
                                <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $doc->file_path }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700 font-semibold uppercase">{{ $doc->category }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <button wire:click="showVersions({{ $doc->id }})" class="px-2 py-0.5 text-xs font-mono font-bold bg-blue-50 text-blue-700 rounded hover:bg-blue-100">
                                    v{{ $doc->version }}
                                </button>
                            </td>
                            <td class="py-4 px-6 text-gray-600 font-medium">{{ $doc->creator->name }}</td>
                            <td class="py-4 px-6 text-center">
                                @php
                                    $badgeColor = match($doc->status) {
                                        'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                                        'pending_signature' => 'bg-amber-50 text-amber-700 border-amber-100',
                                        'signed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                    };
                                    $labelText = match($doc->status) {
                                        'draft' => 'Draft',
                                        'pending_signature' => 'Needs Signature',
                                        'signed' => 'Signed & Sealed',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 inline-flex items-center text-xs font-semibold rounded-full border {{ $badgeColor }}">
                                    {{ $labelText }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right space-x-2">
                                @if($doc->status === 'draft')
                                    <button wire:click="markPendingSignature({{ $doc->id }})" class="text-amber-600 hover:text-amber-900 font-medium text-xs">Request Sign</button>
                                @endif
                                
                                @if($doc->status === 'pending_signature')
                                    <button wire:click="openSignModal({{ $doc->id }})" class="text-emerald-600 hover:text-emerald-950 font-bold text-xs uppercase tracking-wider bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">E-Sign</button>
                                @endif

                                @if($doc->status !== 'signed')
                                    <button wire:click="openModal({{ $doc->id }})" class="text-blue-600 hover:text-blue-900 font-medium text-xs">New Version</button>
                                @endif
                                <button onclick="confirm('Are you sure you want to delete this document permanently?') || event.stopImmediatePropagation()" wire:click="delete({{ $doc->id }})" class="text-red-600 hover:text-red-900 font-medium text-xs">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">No documents in current archives.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $documents->links() }}
        </div>
    </div>

    <!-- Upload Document Modal -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <form wire:submit.prevent="save" class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            {{ $document_id ? 'Upload Document Version' : 'Archive New Document' }}
                        </h3>

                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Document Name / Title</label>
                                <input wire:model="name" type="text" placeholder="e.g. Sales Contract CV. Jaya Sentosa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                @error('name') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Archive Category</label>
                                <select wire:model="doc_category" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm" required>
                                    <option value="General">General</option>
                                    <option value="Contract">Contract / Mou</option>
                                    <option value="Invoice">Invoice / Tax</option>
                                    <option value="HRD">HRD / Personnel</option>
                                </select>
                                @error('doc_category') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Physical File Upload</label>
                                <input type="file" wire:model="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @error('file') <span class="text-xs text-red-600">{{ $message }}</span> @enderror
                            </div>

                            <div class="bg-gray-50 p-3 rounded-md border border-gray-100">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Or Mock File Creation (Testing Fallback)</label>
                                <input wire:model="mock_file_name" type="text" placeholder="e.g. cv_jaya_sentosa_signed_v2.pdf" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">Submit Document</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Version History Drawer/Modal -->
    @if($isVersionsOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeVersionsModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="space-y-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                            Version Logs for: {{ $selectedDoc->name }}
                        </h3>

                        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto pr-2">
                            @forelse($selectedDoc->versions as $v)
                                <div class="py-3 flex items-center justify-between">
                                    <div>
                                        <div class="font-mono font-bold text-gray-800 text-sm">v{{ $v->version }}</div>
                                        <div class="text-xs text-gray-400">{{ $v->file_path }}</div>
                                        <div class="text-xs text-gray-500">Created by: {{ $v->creator->name }} | {{ $v->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                    <button wire:click="restoreVersion({{ $v->id }})" class="px-2.5 py-1 text-xs font-semibold rounded bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200">
                                        Restore
                                    </button>
                                </div>
                            @empty
                                <div class="py-4 text-center text-gray-400 text-sm">No previous versions. Current version v{{ $selectedDoc->version }} is the original.</div>
                            @endforelse
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="button" wire:click="closeVersionsModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Digital E-Sign Canvas Modal -->
    @if($isSignOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true"
             x-data="signaturePad()" x-init="initCanvas()">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeSignModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-middle bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="space-y-4">
                        <div class="border-b border-gray-100 pb-3">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display">
                                Secured E-Sign Document: {{ $selectedDoc->name }}
                            </h3>
                            <p class="text-xs text-gray-500">Sign with your mouse/touchpad inside the secure field below to finalize execution.</p>
                        </div>

                        <!-- Canvas Sign Area -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-2 bg-gray-50">
                            <canvas id="signatureCanvas" class="w-full bg-white rounded border border-gray-200 shadow-inner" 
                                    style="height: 180px;"
                                    @mousedown="startDrawing" @mousemove="draw" @mouseup="stopDrawing" @mouseleave="stopDrawing"
                                    @touchstart="startDrawing" @touchmove="draw" @touchend="stopDrawing"></canvas>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-400">
                            <span>🔒 TLS Secured Signature execution.</span>
                            <button type="button" @click="clearPad" class="font-bold text-red-500 hover:text-red-700">Clear Canvas</button>
                        </div>

                        <!-- Fallback code value input hidden or visual test signature input -->
                        <div class="pt-2">
                            <label class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Encrypted Signature Canvas Reference (Base64)</label>
                            <input type="text" id="sigInput" wire:model="signature_data" class="mt-1 block w-full bg-gray-50 text-[10px] text-gray-500 rounded border-gray-200 py-1" readonly placeholder="Drawing outputs live base64 code here...">
                        </div>

                        <div class="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                            <button type="button" wire:click="closeSignModal" class="px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50">Cancel</button>
                            <button type="button" @click="submitSig" class="px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-700">Finalize & Sign</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function signaturePad() {
        return {
            drawing: false,
            canvas: null,
            ctx: null,
            initCanvas() {
                this.$nextTick(() => {
                    this.canvas = document.getElementById('signatureCanvas');
                    if (this.canvas) {
                        this.ctx = this.canvas.getContext('2d');
                        // Align resolution
                        this.canvas.width = this.canvas.clientWidth;
                        this.canvas.height = this.canvas.clientHeight;
                        this.ctx.lineWidth = 3;
                        this.ctx.lineCap = 'round';
                        this.ctx.strokeStyle = '#1e3a8a'; // Deep Navy signature
                    }
                });
            },
            startDrawing(e) {
                this.drawing = true;
                const pos = this.getPos(e);
                this.ctx.beginPath();
                this.ctx.moveTo(pos.x, pos.y);
            },
            draw(e) {
                if (!this.drawing) return;
                const pos = this.getPos(e);
                this.ctx.lineTo(pos.x, pos.y);
                this.ctx.stroke();
                // Live updates hidden value
                this.updateVal();
            },
            stopDrawing() {
                this.drawing = false;
            },
            getPos(e) {
                const rect = this.canvas.getBoundingClientRect();
                const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return {
                    x: clientX - rect.left,
                    y: clientY - rect.top
                };
            },
            clearPad() {
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                document.getElementById('sigInput').value = '';
                @this.set('signature_data', '');
            },
            updateVal() {
                const dataUrl = this.canvas.toDataURL();
                document.getElementById('sigInput').value = dataUrl;
                @this.set('signature_data', dataUrl, true);
            },
            submitSig() {
                const val = document.getElementById('sigInput').value;
                if (!val) {
                    alert('Please write your signature on the pad first!');
                    return;
                }
                @this.set('signature_data', val);
                @this.call('saveSignature');
            }
        }
    }
</script>
