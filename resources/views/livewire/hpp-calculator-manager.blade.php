<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900">Kalkulator HPP Impor</h1>
            <p class="text-sm text-gray-500 mt-1">Hitung Harga Pokok Penjualan (HPP) barang impor secara akurat dari China ke Indonesia berdasarkan formula terstandardisasi.</p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="create" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150 shadow-sm shadow-blue-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Buat Kalkulasi Baru
            </button>
        </div>
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex justify-between items-center transition-all duration-300 shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('info'))
        <div class="p-4 text-sm text-blue-800 rounded-lg bg-blue-50 border border-blue-200 flex justify-between items-center transition-all duration-300 shadow-sm" role="alert">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span class="font-semibold">{{ session('info') }}</span>
            </div>
        </div>
    @endif

    <!-- Interactive Active Calculator Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Input Panel -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-6">
            <div class="border-b border-gray-100 pb-4">
                <h2 class="text-lg font-bold font-display text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    Panel Kalkulator Aktif
                </h2>
                <p class="text-xs text-gray-400 mt-1">Ubah nilai-nilai di bawah ini untuk melihat HPP terhitung secara instan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Product Selector (Optional Link) -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Link ke Produk ERP (Opsional)</label>
                    <div class="mt-1 relative">
                        <select wire:model.live="selected_product_id" class="block w-full border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white">
                            <option value="">-- Pilih Produk Master untuk Memuat Nama & Harga --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Harga: Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Nama Barang -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang / Deskripsi</label>
                    <input type="text" wire:model.live="nama_barang" placeholder="Contoh: Lampu LED Outdoor Waterproof" class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-3.5 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('nama_barang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Harga Barang -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-1.5 inline-block"></span>
                        Harga Barang (Cina / Supplier)
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="harga_barang" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('harga_barang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Ongkir Supplier ke Forwarder -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 mr-1.5 inline-block"></span>
                        Ongkir ke Gudang Forwarder (Cina)
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="ongkir_supplier_to_forwarder" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('ongkir_supplier_to_forwarder') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Tax Refund -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-1.5 inline-block"></span>
                        Tax Refund (Pengembalian Pajak)
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="tax_refund" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500" placeholder="Negatif untuk mengurangi">
                    </div>
                    <span class="text-[10px] text-gray-400 block mt-1">Masukkan angka negatif jika mengurangi biaya (misal: -50000).</span>
                    @error('tax_refund') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Ongkir ke Indonesia dari Cina -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-purple-500 mr-1.5 inline-block"></span>
                        Ongkir Cina ke Indonesia
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="ongkir_china_to_indonesia" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('ongkir_china_to_indonesia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Pajak Impor / PPN / PPh / VAT -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-500 mr-1.5 inline-block"></span>
                        Pajak Impor / PPN / PPh / VAT
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="pajak_impor" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('pajak_impor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <!-- Margin Keuntungan -->
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider flex items-center">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-1.5 inline-block"></span>
                        Margin / Mark-up Keuntungan
                    </label>
                    <div class="mt-1 relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 text-sm">Rp</span>
                        </div>
                        <input type="number" wire:model.live="margin" class="pl-9 block w-full border border-gray-300 rounded-lg py-2.5 text-sm font-semibold focus:ring-2 focus:ring-blue-500">
                    </div>
                    @error('margin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Action buttons inside the active panel -->
            <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                <button type="button" wire:click="resetInputFields" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-300 rounded-lg transition duration-150">
                    Reset Form
                </button>

                <button type="button" wire:click="store" class="inline-flex items-center px-5 py-2.5 bg-blue-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-blue-700 active:bg-blue-900 transition ease-in-out duration-150 shadow-md shadow-blue-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Simpan ke Riwayat
                </button>
            </div>
        </div>

        <!-- Real-Time Visualization / Summary Panel -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between">
            <div class="space-y-6">
                <div class="border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold font-display text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.003 9.003 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        Analisis HPP Real-Time
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">Struktur persentase biaya pembentuk HPP.</p>
                </div>

                <!-- Hero HPP Value Display -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-6 text-white shadow-lg shadow-blue-100 relative overflow-hidden">
                    <div class="absolute -right-10 -bottom-10 opacity-15">
                        <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2V7.414A2 2 0 0015.414 6L12 2.586A2 2 0 0010.586 2H6zm5 6a1 1 0 10-2 0v3.586l-1.293-1.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <span class="text-xs text-blue-100 uppercase tracking-widest font-semibold block">Total HPP Terhitung</span>
                    <h3 class="text-3xl font-extrabold font-display mt-2">Rp {{ number_format($total_hpp, 0, ',', '.') }}</h3>
                    <p class="text-[10px] text-blue-200 mt-3 italic">HPP = Harga + Ongkir Fwd + Tax Refund + Ongkir Indo + Pajak + Margin</p>
                </div>

                <!-- Cost Components Share (Horizontal Stacked Bar Chart) -->
                @php
                    $sumFields = abs($harga_barang) + abs($ongkir_supplier_to_forwarder) + abs($tax_refund) + abs($ongkir_china_to_indonesia) + abs($pajak_impor) + abs($margin);
                    
                    $pctHarga = $sumFields > 0 ? round((abs($harga_barang) / $sumFields) * 100) : 0;
                    $pctOngkirSupp = $sumFields > 0 ? round((abs($ongkir_supplier_to_forwarder) / $sumFields) * 100) : 0;
                    $pctTax = $sumFields > 0 ? round((abs($tax_refund) / $sumFields) * 100) : 0;
                    $pctOngkirChina = $sumFields > 0 ? round((abs($ongkir_china_to_indonesia) / $sumFields) * 100) : 0;
                    $pctPajak = $sumFields > 0 ? round((abs($pajak_impor) / $sumFields) * 100) : 0;
                    $pctMargin = $sumFields > 0 ? round((abs($margin) / $sumFields) * 100) : 0;
                @endphp

                <div class="space-y-2">
                    <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider block">Proporsi Struktur Biaya</span>
                    
                    @if($sumFields === 0)
                        <div class="h-8 bg-gray-100 rounded-lg flex items-center justify-center text-xs text-gray-400 border border-dashed border-gray-200">
                            Masukkan angka untuk memulai visualisasi
                        </div>
                    @else
                        <!-- Progress Bar Stacked -->
                        <div class="h-7 w-full rounded-lg overflow-hidden flex bg-gray-100 shadow-inner">
                            @if($pctHarga > 0)
                                <div style="width: {{ $pctHarga }}%" class="bg-blue-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Harga Barang: {{ $pctHarga }}%">
                                    {{ $pctHarga }}%
                                </div>
                            @endif
                            @if($pctOngkirSupp > 0)
                                <div style="width: {{ $pctOngkirSupp }}%" class="bg-yellow-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Ongkir ke Forwarder: {{ $pctOngkirSupp }}%">
                                    {{ $pctOngkirSupp }}%
                                </div>
                            @endif
                            @if($pctTax > 0)
                                <div style="width: {{ $pctTax }}%" class="bg-rose-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Tax Refund: {{ $pctTax }}%">
                                    {{ $pctTax }}%
                                </div>
                            @endif
                            @if($pctOngkirChina > 0)
                                <div style="width: {{ $pctOngkirChina }}%" class="bg-purple-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Ongkir China-Indo: {{ $pctOngkirChina }}%">
                                    {{ $pctOngkirChina }}%
                                </div>
                            @endif
                            @if($pctPajak > 0)
                                <div style="width: {{ $pctPajak }}%" class="bg-cyan-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Pajak Impor: {{ $pctPajak }}%">
                                    {{ $pctPajak }}%
                                </div>
                            @endif
                            @if($pctMargin > 0)
                                <div style="width: {{ $pctMargin }}%" class="bg-emerald-500 h-full flex items-center justify-center text-[10px] text-white font-bold transition-all duration-300" title="Margin: {{ $pctMargin }}%">
                                    {{ $pctMargin }}%
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Breakdown Legends and actual values -->
                <div class="space-y-2.5 font-sans pt-2">
                    <!-- Harga Barang -->
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-blue-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Harga Barang</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">Rp {{ number_format($harga_barang, 0, ',', '.') }}</span>
                    </div>

                    <!-- Ongkir Gudang Forwarder -->
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-yellow-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Ongkir ke Forwarder</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">Rp {{ number_format($ongkir_supplier_to_forwarder, 0, ',', '.') }}</span>
                    </div>

                    <!-- Tax Refund -->
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-rose-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Tax Refund</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800 @if($tax_refund < 0) text-rose-600 @endif">
                            Rp {{ number_format($tax_refund, 0, ',', '.') }}
                        </span>
                    </div>

                    <!-- Ongkir China to Indonesia -->
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-purple-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Ongkir Cina-Indo</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">Rp {{ number_format($ongkir_china_to_indonesia, 0, ',', '.') }}</span>
                    </div>

                    <!-- Pajak Impor -->
                    <div class="flex justify-between items-center text-xs">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-cyan-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Pajak Impor/VAT</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">Rp {{ number_format($pajak_impor, 0, ',', '.') }}</span>
                    </div>

                    <!-- Margin -->
                    <div class="flex justify-between items-center text-xs border-b border-gray-100 pb-2">
                        <div class="flex items-center space-x-2">
                            <span class="w-3 h-3 rounded bg-emerald-500 inline-block"></span>
                            <span class="text-gray-600 font-medium">Margin Keuntungan</span>
                        </div>
                        <span class="font-mono font-bold text-gray-800">Rp {{ number_format($margin, 0, ',', '.') }}</span>
                    </div>

                    <!-- Grand Total Math representation -->
                    <div class="flex justify-between items-center text-sm pt-1.5 font-bold">
                        <span class="text-gray-900 font-display">Hasil HPP Akhir:</span>
                        <span class="font-mono text-blue-700">Rp {{ number_format($total_hpp, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Chinese-Indonesian Custom Import Tips Card -->
            <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 mt-6">
                <h4 class="text-xs font-bold text-gray-800 flex items-center mb-1 uppercase tracking-wider font-display">
                    <svg class="w-4 h-4 mr-1 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                    Informasi Importir
                </h4>
                <p class="text-[11px] text-gray-500 leading-relaxed">
                    Pastikan PPN (11%) dan PPh (2.5% s/d 7.5% untuk API/Non-API) dihitung dengan tepat dari Nilai Impor (CIF + Bea Masuk). Gunakan tax refund dari ekspor China bila didukung oleh supplier forwarder Anda.
                </p>
            </div>
        </div>
    </div>

    <!-- Calculations History Registry Section -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-4">
            <div>
                <h2 class="text-lg font-bold font-display text-gray-900">Riwayat Kalkulasi HPP</h2>
                <p class="text-xs text-gray-500 mt-1">Daftar perhitungan HPP yang telah disimpan dalam basis data.</p>
            </div>
            <!-- Search field -->
            <div class="w-full sm:max-w-xs relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50" placeholder="Cari nama barang...">
            </div>
        </div>

        <!-- Calculations Table -->
        <div class="overflow-hidden rounded-lg border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-xs font-sans">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500 border-b border-gray-200">
                            <th class="py-3 px-4 font-display">Nama Barang</th>
                            <th class="py-3 px-4 text-right font-display">Harga Barang</th>
                            <th class="py-3 px-4 text-right font-display">Ongkir Forwarder</th>
                            <th class="py-3 px-4 text-right font-display">Tax Refund</th>
                            <th class="py-3 px-4 text-right font-display">Ongkir Indo</th>
                            <th class="py-3 px-4 text-right font-display">Pajak Impor</th>
                            <th class="py-3 px-4 text-right font-display">Margin</th>
                            <th class="py-3 px-4 text-right font-display text-blue-700 bg-blue-50/50">Total HPP</th>
                            <th class="py-3 px-4 text-center font-display">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($calculations as $calc)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-3 px-4 font-bold text-gray-900 max-w-[150px] truncate" title="{{ $calc->nama_barang }}">
                                    {{ $calc->nama_barang }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-gray-700">Rp {{ number_format($calc->harga_barang, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-gray-700">Rp {{ number_format($calc->ongkir_supplier_to_forwarder, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-medium @if($calc->tax_refund < 0) text-rose-600 @else text-gray-700 @endif">
                                    Rp {{ number_format($calc->tax_refund, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-gray-700">Rp {{ number_format($calc->ongkir_china_to_indonesia, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-gray-700">Rp {{ number_format($calc->pajak_impor, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-medium text-gray-700">Rp {{ number_format($calc->margin, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-blue-700 bg-blue-50/20">Rp {{ number_format($calc->total_hpp, 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-center space-x-2 whitespace-nowrap">
                                    <button wire:click="loadCalculation({{ $calc->id }})" class="inline-flex items-center px-2.5 py-1 text-[10px] font-semibold rounded bg-blue-50 text-blue-700 hover:bg-blue-100 transition duration-150 focus:outline-none">
                                        Muat Kalkulator
                                    </button>
                                    <button wire:click="edit({{ $calc->id }})" class="inline-flex items-center px-2 py-1 text-[10px] font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded transition duration-150">
                                        Edit
                                    </button>
                                    <button wire:click="delete({{ $calc->id }})" wire:confirm="Hapus kalkulasi ini dari riwayat?" class="inline-flex items-center px-2 py-1 text-[10px] font-semibold text-red-600 hover:text-red-900 hover:bg-red-50 rounded transition duration-150">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="py-12 text-center text-gray-500 font-medium">
                                    Tidak ada riwayat kalkulasi yang tersimpan. Mulai menghitung dan simpan di panel atas!
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($calculations->hasPages())
                <div class="px-6 py-3 border-t border-gray-200 bg-gray-50">
                    {{ $calculations->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modals Section for Editing -->
    @if($isOpen && $isEditMode)
        <div class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-xl px-6 pt-5 pb-6 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-xl leading-6 font-bold text-gray-900 font-display flex items-center border-b border-gray-100 pb-4">
                            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Kalkulasi HPP
                        </h3>
                        
                        <div class="mt-4 space-y-4 font-sans">
                            <!-- Nama Barang -->
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Barang / Deskripsi</label>
                                <input type="text" wire:model.live="nama_barang" class="mt-1 block w-full border border-gray-300 rounded-lg py-2.5 px-3 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                @error('nama_barang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Harga Barang -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Barang</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="harga_barang" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('harga_barang') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Ongkir ke Forwarder -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Ongkir ke Gudang Forwarder</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="ongkir_supplier_to_forwarder" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('ongkir_supplier_to_forwarder') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Tax Refund -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Tax Refund (Ekspor China)</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="tax_refund" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('tax_refund') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Ongkir China-Indo -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Ongkir Cina ke Indonesia</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="ongkir_china_to_indonesia" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('ongkir_china_to_indonesia') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Pajak Impor -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Pajak Impor / VAT / PPN</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="pajak_impor" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('pajak_impor') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <!-- Margin -->
                                <div>
                                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider">Margin Keuntungan</label>
                                    <div class="mt-1 relative rounded-lg shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-400 text-sm">Rp</span>
                                        </div>
                                        <input type="number" wire:model.live="margin" class="pl-9 block w-full border border-gray-300 rounded-lg py-2 sm:text-sm focus:ring-2 focus:ring-blue-500">
                                    </div>
                                    @error('margin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Real-time dynamic calculation in modal -->
                            <div class="mt-4 bg-blue-50 rounded-lg p-4 flex justify-between items-center border border-blue-100">
                                <span class="text-sm font-semibold text-blue-900">HPP Terhitung (Real-Time):</span>
                                <span class="text-xl font-bold font-mono text-blue-700">Rp {{ number_format($total_hpp, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3 border-t border-gray-100 pt-4">
                        <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none transition duration-150">
                            Batal
                        </button>
                        <button type="button" wire:click="store" class="inline-flex justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:outline-none transition duration-150 shadow-sm shadow-blue-200">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
