<div>
    <!-- Breadcrumb & Top Bar -->
    <div class="mb-6">
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
            <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span>/</span>
            <a href="{{ route('rentals') }}" wire:navigate class="hover:text-blue-600 transition-colors">Rentals</a>
            <span>/</span>
            <span class="text-gray-800 font-medium">{{ $isEditMode ? 'Edit Order (' . $rental_number . ')' : 'Buat Order Baru' }}</span>
        </div>

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('rentals') }}" 
                   wire:navigate
                   class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-gray-200 bg-white text-gray-600 hover:bg-gray-50 hover:text-gray-900 shadow-2xs transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                        {{ $isEditMode ? 'Edit Order Rental: ' . $rental_number : 'Buat Order Rental Baru' }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-0.5">
                        {{ $isEditMode ? 'Perbarui informasi penyewa, tarif, durasi atau status pengembalian barang.' : 'Formulir pencatatan transaksi sewa barang, alat, atau armada perusahaan.' }}
                    </p>
                </div>
            </div>

            <!-- Header Quick Actions -->
            <div class="flex items-center gap-2.5">
                <button type="button" 
                        wire:click="cancel" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none transition-colors">
                    Batal
                </button>
                <button type="button" 
                        wire:click="save" 
                        class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center gap-1.5 cursor-pointer">
                    <span wire:loading.remove wire:target="save">
                        {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Order Rental' }}
                    </span>
                    <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Menyimpan...
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Grid Form -->
    <form wire:submit.prevent="save">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Columns: Main Input Cards -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Card 1: Customer & Item Information -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs">1</span>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Informasi Penyewa & Barang Sewa</h3>
                        </div>
                        <span class="text-xs text-gray-400 font-mono">{{ $rental_number }}</span>
                    </div>

                    <div class="p-6 space-y-5">
                        <!-- Nomor Rental & Customer -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">No. Rental Order</label>
                                <input type="text" 
                                       wire:model="rental_number" 
                                       placeholder="Auto-generated" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-gray-50 font-mono text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @error('rental_number') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Customer / Penyewa <span class="text-red-500">*</span></label>
                                <select wire:model="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Pilih Customer --</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">
                                            {{ $c->name }} {{ $c->company_name ? '('.$c->company_name.')' : '' }} {{ $c->phone ? ' - ' . $c->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('customer_id') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Inventaris Produk & Nama Barang -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Produk Inventaris (Opsional)</label>
                                <select wire:model.live="product_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">-- Input Bebas / Custom Item --</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}">{{ $p->name }} (Rp {{ number_format($p->price, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                                <span class="text-[11px] text-gray-400 mt-1 block">Memilih produk akan otomatis mengisi nama barang & tarif standar.</span>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Nama Barang / Unit Sewa <span class="text-red-500">*</span></label>
                                <input type="text" 
                                       wire:model="item_name" 
                                       placeholder="Contoh: Kamera Sony A7IV + Lensa 24-70mm" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('item_name') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Catatan Kondisi Keluar -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Kondisi Fisik Saat Serah Terima (Keluar)</label>
                            <textarea wire:model="condition_out" 
                                      rows="2" 
                                      placeholder="Contoh: Unit mulus 95%, baterai 2 pcs, charger ori, tas kamera, ada gores tipis di bodi samping..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Schedule & Duration -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs">2</span>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Jadwal & Durasi Sewa</h3>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Mulai Sewa <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       wire:model.live="start_date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                @error('start_date') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Target Selesai <span class="text-red-500">*</span></label>
                                <input type="date" 
                                       wire:model.live="end_date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                @error('end_date') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Satuan Waktu Sewa</label>
                                <select wire:model.live="rate_unit" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                    <option value="hourly">Per Jam (Hourly)</option>
                                    <option value="daily">Harian (Daily)</option>
                                    <option value="weekly">Mingguan (Weekly)</option>
                                    <option value="monthly">Bulanan (Monthly)</option>
                                </select>
                                @error('rate_unit') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Lama Durasi Sewa <span class="text-red-500">*</span></label>
                                <input type="number" 
                                       step="1" 
                                       min="1" 
                                       wire:model.live="duration" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                @error('duration') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 3: Status & Conditions -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-xs">3</span>
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Status & Catatan Pengembalian</h3>
                        </div>
                    </div>

                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Rental</label>
                                <select wire:model="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                    <option value="draft">Draft</option>
                                    <option value="confirmed">Terkonfirmasi (Confirmed)</option>
                                    <option value="ongoing">Sedang Disewa (Ongoing)</option>
                                    <option value="returned">Dikembalikan (Returned)</option>
                                    <option value="completed">Selesai (Completed)</option>
                                    <option value="cancelled">Dibatalkan (Cancelled)</option>
                                </select>
                                @error('status') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Status Pembayaran</label>
                                <select wire:model="payment_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                    <option value="unpaid">Belum Lunas (Unpaid)</option>
                                    <option value="partially_paid">Bayar Sebagian (Partially Paid)</option>
                                    <option value="paid">Lunas (Paid)</option>
                                </select>
                                @error('payment_status') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tanggal Aktual Kembali</label>
                                <input type="date" 
                                       wire:model="return_date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500">
                                @error('return_date') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan Kondisi Fisik Saat Pengembalian (Masuk)</label>
                            <textarea wire:model="condition_in" 
                                      rows="2" 
                                      placeholder="Contoh: Unit dikembalikan dalam kondisi bersih, baterai terisi, tidak ada kerusakan baru..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Catatan & Perjanjian Sewa</label>
                            <textarea wire:model="notes" 
                                      rows="2" 
                                      placeholder="Catatan tambahan, identitas penjamin (KTP/SIM), instruksi pengantaran..." 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Column: Pricing & Calculation Sidebar -->
            <div class="space-y-6">
                <!-- Calculation Summary Card -->
                <div class="bg-white rounded-xl shadow-xs border border-gray-200 overflow-hidden sticky top-6">
                    <div class="px-5 py-4 bg-gray-50/80 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-xs font-bold text-gray-700 uppercase tracking-wider">Kalkulasi Tarif & Biaya</h3>
                        <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700 font-semibold rounded-full border border-blue-200">
                            Auto Kalkulasi
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Unit Quantity & Rate Input -->
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jumlah Unit <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" 
                                       step="1" 
                                       min="1" 
                                       wire:model.live="quantity" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-medium focus:ring-2 focus:ring-blue-500">
                                <span class="absolute inset-y-0 right-0 pr-3 flex items-center text-xs text-gray-400 font-medium">unit</span>
                            </div>
                            @error('quantity') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">
                                Tarif Sewa (per unit / {{ match($rate_unit) { 'hourly' => 'jam', 'weekly' => 'minggu', 'monthly' => 'bulan', default => 'hari' } }}) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500 font-medium">Rp</span>
                                <input type="number" 
                                       step="any" 
                                       min="0" 
                                       wire:model.live="rental_rate" 
                                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-mono font-medium focus:ring-2 focus:ring-blue-500">
                            </div>
                            @error('rental_rate') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Biaya Kirim / Setup (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500 font-medium">Rp</span>
                                <input type="number" 
                                       step="any" 
                                       min="0" 
                                       wire:model.live="delivery_fee" 
                                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-mono focus:ring-2 focus:ring-blue-500">
                            </div>
                            @error('delivery_fee') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Uang Jaminan / Deposit (Rp)</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs text-gray-500 font-medium">Rp</span>
                                <input type="number" 
                                       step="any" 
                                       min="0" 
                                       wire:model="deposit_amount" 
                                       class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg text-sm bg-white font-mono focus:ring-2 focus:ring-blue-500">
                            </div>
                            <span class="text-[11px] text-gray-400 mt-1 block">Jaminan dikembalikan saat barang kembali aman.</span>
                            @error('deposit_amount') <span class="text-xs text-red-600 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Calculation Breakdown Table -->
                        <div class="pt-3 border-t border-gray-100 space-y-2 text-xs">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal Sewa</span>
                                <span class="font-mono font-medium">
                                    Rp {{ number_format(floatval($quantity) * floatval($rental_rate) * intval($duration), 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Biaya Kirim</span>
                                <span class="font-mono font-medium">
                                    Rp {{ number_format(floatval($delivery_fee), 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-amber-700 pt-1 border-t border-dashed border-gray-200">
                                <span>Uang Jaminan (Deposit)</span>
                                <span class="font-mono font-semibold">
                                    Rp {{ number_format(floatval($deposit_amount), 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Grand Total Highlight Card -->
                        <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                            <span class="text-xs font-bold text-blue-900 uppercase block tracking-wider">Total Tagihan Sewa</span>
                            <div class="text-2xl font-bold font-mono text-blue-800 mt-1">
                                Rp {{ number_format($total_amount, 0, ',', '.') }}
                            </div>
                            <span class="text-[11px] text-blue-600 mt-1 block">
                                {{ $quantity }} unit &times; {{ $duration }} {{ match($rate_unit) { 'hourly' => 'jam', 'weekly' => 'minggu', 'monthly' => 'bulan', default => 'hari' } }} + Kirim
                            </span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-2 space-y-2">
                            <button type="submit" 
                                    class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 flex items-center justify-center gap-1.5 cursor-pointer">
                                <span wire:loading.remove wire:target="save">
                                    {{ $isEditMode ? 'Simpan Perubahan' : 'Buat Order Rental' }}
                                </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-1.5">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                    Menyimpan...
                                </span>
                            </button>

                            <button type="button" 
                                    wire:click="cancel" 
                                    class="w-full py-2 px-4 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 focus:outline-none transition-colors text-center block">
                                Kembali ke Daftar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
