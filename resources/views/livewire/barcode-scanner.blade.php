<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Barcode & SKU Scanner Terminal</h1>
        <p class="text-sm text-gray-500 mt-1">Simulate hardware barcode gun inputs, manage warehouse stock intakes, or run inventory inspections live.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Side: Scanner Config & Mock Hardware Gun -->
        <div class="space-y-6 lg:col-span-1">
            
            <!-- Scanner Configuration Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-md font-bold text-gray-900 mb-4 font-display">Scanner Settings</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Intake Target Warehouse</label>
                        <select wire:model.live="selectedWarehouseId" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}">{{ $wh->warehouse_name }} ({{ $wh->warehouse_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Operation Type</label>
                        <div class="grid grid-cols-2 gap-2 mt-1">
                            <button wire:click="$set('operation', 'intake')" 
                                    class="py-2 px-3 text-xs font-semibold rounded border transition text-center {{ $operation === 'intake' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                Stock Intake / GR
                            </button>
                            <button wire:click="$set('operation', 'count')" 
                                    class="py-2 px-3 text-xs font-semibold rounded border transition text-center {{ $operation === 'count' ? 'bg-blue-600 border-blue-600 text-white' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50' }}">
                                Audit Count Only
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mock Hardware Scanner Viewport -->
            <div class="bg-gray-100 rounded-lg shadow-lg border border-gray-300 p-6 text-black relative overflow-hidden flex flex-col justify-between" style="min-height: 280px;">
                
                <!-- Scanner Animation Guide Lines -->
                <div class="absolute inset-x-0 h-0.5 bg-red-500 opacity-75 shadow-[0_0_8px_rgba(239,68,68,1)]"
                     style="top: 50%; animation: scanPulse 2.5s infinite ease-in-out;"></div>
                
                <div class="flex items-center justify-between text-xs text-gray-700 z-10">
                    <span class="flex items-center font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-ping mr-2"></span>
                        TERMINAL ACTIVE
                    </span>
                    <span class="font-mono text-[10px] text-gray-600">WAVELENGTH: 650nm</span>
                </div>

                <!-- Simulation Info inside Screen -->
                <div class="my-auto text-center space-y-2 z-10">
                    <div class="font-mono text-xs text-emerald-800 font-bold">
                        [ WAITING FOR BARCODE GUN SIGNAL ]
                    </div>
                    <div class="text-[10px] text-gray-700 uppercase tracking-widest max-w-[200px] mx-auto font-medium">
                        Focus input field below and pull hardware scanner trigger or use simulated trigger.
                    </div>
                </div>

                <!-- Hidden Input auto focused -->
                <div class="z-10 mt-auto">
                    <form wire:submit.prevent="processScan" class="relative">
                        <input wire:model="scanInput" type="text" autofocus id="scannerTriggerField"
                               placeholder="Scan Barcode (Enter SKU Code)..." 
                               class="w-full bg-white border border-gray-300 text-black placeholder-gray-500 rounded text-xs font-mono text-center focus:ring-emerald-500 focus:border-emerald-500 py-2">
                    </form>
                </div>
            </div>

            <!-- Simulated Scan Trigger Box for Demonstration -->
            <div class="bg-white rounded-lg shadow-sm border border-emerald-100 p-6">
                <h3 class="text-md font-bold text-gray-900 mb-2 font-display">Simulated Scan Trigger</h3>
                <p class="text-xs text-gray-500 mb-4">Click below to simulate hardware scanner gun registering a barcode reading.</p>
                
                <div class="space-y-3">
                    <select wire:model="simulatedProductCode" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 text-sm">
                        <option value="">Choose product to scan...</option>
                        @foreach($products as $p)
                            <option value="{{ $p->code }}">{{ $p->name }} [{{ $p->code }}]</option>
                        @endforeach
                    </select>

                    <button wire:click="triggerSimulatedScan" class="w-full py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider rounded transition">
                        ⚡ Pull Simulated Trigger
                    </button>
                </div>
            </div>

        </div>

        <!-- Right Side: Scanned Pipeline Feed -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Scan Alerts -->
            @if(session()->has('scan_success'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-md animate-fade-in flex items-center justify-between">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-emerald-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-sm font-semibold text-emerald-800">{{ session('scan_success') }}</p>
                    </div>
                    <!-- Audio Beep Effect -->
                    <audio src="https://assets.mixkit.co/active_storage/sfx/2869/2869-600.wav" autoplay class="hidden"></audio>
                </div>
            @endif

            @if(session()->has('scan_error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-md animate-fade-in">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <p class="text-sm font-semibold text-red-800">{{ session('scan_error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Scanned Items Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-md font-bold text-gray-900 font-display">Scan Session Feed</h3>
                        <p class="text-xs text-gray-500">Live listings of scanned packages in this console session.</p>
                    </div>
                    <button wire:click="clearScans" class="text-xs font-semibold text-gray-500 hover:text-red-600 transition">
                        Clear Log
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs tracking-wider font-mono">
                            <tr>
                                <th class="py-3 px-6">Timestamp</th>
                                <th class="py-3 px-6">Barcode / SKU</th>
                                <th class="py-3 px-6">Product Name</th>
                                <th class="py-3 px-6">Scan Action</th>
                                <th class="py-3 px-6 text-center">New Total Stock</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-mono text-xs">
                            @forelse($scannedItems as $item)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="py-3.5 px-6 text-gray-500">{{ $item['timestamp'] }}</td>
                                    <td class="py-3.5 px-6 font-bold text-blue-700">{{ $item['code'] }}</td>
                                    <td class="py-3.5 px-6 font-bold text-gray-900">{{ $item['name'] }}</td>
                                    <td class="py-3.5 px-6 text-gray-700">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ str_contains($item['action'], '+1') ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $item['action'] }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-6 text-center text-gray-800 font-bold font-mono">{{ $item['current_stock'] }} {{ $item['unit'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400 font-sans">
                                        No barcode scanned yet. Target the red laser scanner to start log streams.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>
    <style>
        @keyframes scanPulse {
            0%, 100% {
                top: 10%;
            }
            50% {
                top: 90%;
            }
        }
    </style>
</div>
