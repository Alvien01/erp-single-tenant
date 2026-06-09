<div x-data="{ activeTab: 'products', cartCount: {{ count($cart) }} }" 
     x-effect="cartCount = {{ count($cart) }}" 
     class="flex-1 flex flex-col lg:flex-row w-full h-full relative overflow-hidden">
     
    <!-- MOBILE TAB BAR -->
    <div class="lg:hidden flex bg-slate-900 border-b border-slate-800 shrink-0 z-20">
        <button @click="activeTab = 'products'" 
                :class="activeTab === 'products' ? 'text-blue-400 border-b-2 border-blue-500 font-semibold bg-slate-800/10' : 'text-slate-400 font-medium hover:text-slate-200'"
                class="flex-1 py-3 text-center text-sm transition-all flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
            <span>Produk</span>
        </button>
        <button @click="activeTab = 'cart'" 
                :class="activeTab === 'cart' ? 'text-blue-400 border-b-2 border-blue-500 font-semibold bg-slate-800/10' : 'text-slate-400 font-medium hover:text-slate-200'"
                class="flex-1 py-3 text-center text-sm transition-all flex items-center justify-center gap-2 relative">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Keranjang</span>
            <span class="bg-blue-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full ml-1" 
                  x-show="cartCount > 0" x-text="cartCount" x-cloak>
            </span>
        </button>
    </div>
    
    <!-- IF NO ACTIVE SESSION -->
    @if(!$currentSession)
    <div class="absolute inset-0 z-50 glass-panel flex flex-col items-center justify-center p-4 overflow-y-auto">
        <div class="bg-slate-800 border border-slate-700 p-6 sm:p-8 rounded-2xl shadow-2xl max-w-md w-full text-center my-auto">
            <div class="w-20 h-20 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h2 class="text-2xl font-display font-bold text-white mb-2">Shift Kasir Ditutup</h2>
            <p class="text-slate-400 mb-8 text-sm">Anda harus membuka shift kasir (Open Session) terlebih dahulu sebelum memulai transaksi di POS.</p>
            
            <div class="space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Store / Cabang</label>
                    <div class="px-4 py-3 bg-slate-900 border border-slate-700 rounded-lg text-slate-300">
                        {{ $currentStore ? $currentStore->name : 'Tidak ada store aktif' }}
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Saldo Kas Awal (Modal)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-slate-400 font-medium">Rp</span>
                        <input type="number" wire:model="openingCash" class="w-full bg-slate-900 border border-slate-700 rounded-lg pl-12 pr-4 py-3 text-white focus:ring-blue-500 focus:border-blue-500" placeholder="0">
                    </div>
                </div>
                <button wire:click="openSession" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold shadow-lg shadow-blue-500/20 transition-all active:scale-[0.98]">
                    Buka Shift Kasir
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- LEFT PANEL: Product Grid & Tools -->
    <div :class="activeTab === 'products' ? 'flex' : 'hidden lg:flex'" 
         class="flex-1 flex-col bg-slate-900/50 border-r border-slate-800 overflow-hidden w-full">
        
        <!-- Search & Filter Bar -->
        <div class="p-4 border-b border-slate-800 bg-slate-900/80 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-3 w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search" class="w-full bg-slate-800 border-none rounded-xl pl-10 pr-4 py-2.5 text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500" placeholder="Cari produk, scan barcode...">
            </div>
            
            <div class="flex gap-2 shrink-0 overflow-x-auto no-scrollbar">
                <button wire:click="$set('categoryFilter', '')" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-colors {{ $categoryFilter === '' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    Semua
                </button>
                @foreach($categories as $cat)
                <button wire:click="$set('categoryFilter', {{ $cat->id }})" class="px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap transition-colors {{ (int)$categoryFilter === $cat->id ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }}">
                    {{ $cat->name }}
                </button>
                @endforeach
            </div>
        </div>

        <!-- System Messages -->
        @if(session('error'))
        <div class="mx-4 mt-4 p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            {{ session('error') }}
        </div>
        @endif
        @if(session('success'))
        <div class="mx-4 mt-4 p-3 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm flex items-center gap-2">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            {{ session('success') }}
        </div>
        @endif

        <!-- Products Grid -->
        <div class="flex-1 overflow-y-auto p-3 sm:p-4 content-start">
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-5 2xl:grid-cols-6 gap-3 sm:gap-4">
                @forelse($products as $product)
                <div wire:click="addToCart({{ $product->id }})" class="bg-slate-800/80 hover:bg-slate-700 border border-slate-700/50 hover:border-blue-500/50 rounded-xl overflow-hidden cursor-pointer transition-all duration-200 group flex flex-col h-full">
                    <!-- Product Image / Placeholder -->
                    <div class="h-28 bg-slate-900 relative flex items-center justify-center p-2">
                        @php
                            $imageUrl = null;
                            if (!empty($product->image)) {
                                $img = is_array($product->image) ? $product->image : json_decode($product->image, true);
                                if (is_array($img) && !empty($img)) {
                                    $firstImage = reset($img);
                                    $imageUrl = asset('storage/' . $firstImage);
                                }
                            }
                        @endphp

                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="h-full object-contain group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center text-slate-500 font-display font-bold text-xl">
                                {{ substr($product->name, 0, 1) }}
                            </div>
                        @endif
                        
                        <!-- Stock Badge -->
                        <div class="absolute top-2 right-2 px-2 py-0.5 rounded text-[10px] font-bold {{ ($product->stock ?? 0) > 5 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-red-500/20 text-red-400' }}">
                            {{ ($product->stock ?? 0) + 0 }} {{ $product->unit ?? '' }}
                        </div>
                    </div>
                    
                    <!-- Product Info -->
                    <div class="p-3 flex flex-col flex-1">
                        <div class="text-xs text-slate-400 font-mono mb-1 truncate">{{ $product->code ?? '' }}</div>
                        <h3 class="text-sm font-medium text-slate-200 leading-tight mb-2 line-clamp-2">{{ $product->name }}</h3>
                        <div class="mt-auto font-mono font-bold text-blue-400 text-sm">
                            Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-12 flex flex-col items-center justify-center text-slate-500">
                    <svg class="w-16 h-16 mb-4 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="font-medium text-lg">Produk tidak ditemukan</p>
                    <p class="text-sm">Coba kata kunci lain atau scan barcode</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- POS Bottom Toolbar -->
        <div class="h-14 bg-slate-900 border-t border-slate-800 flex items-center justify-between px-4 shrink-0">
            <div class="flex gap-2">
                <button wire:click="$set('showPendingModal', true)" class="flex items-center gap-2 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="hidden sm:inline">Pending Orders</span>
                    @if(isset($pendingOrders) && $pendingOrders->count() > 0)
                    <span class="bg-amber-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded ml-1">{{ $pendingOrders->count() }}</span>
                    @endif
                </button>
            </div>
            
            <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-400">
                <span class="flex items-center gap-1">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div> Online
                </span>
                <span class="text-slate-700">|</span>
                <button wire:click="closeSession" class="text-red-400 hover:text-red-300 font-semibold transition-colors">Close Shift</button>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Cart & Checkout -->
    <div :class="activeTab === 'cart' ? 'flex' : 'hidden lg:flex'" 
         class="w-full lg:w-[400px] flex-col bg-slate-800 border-l border-slate-700 shrink-0 z-10 shadow-[-10px_0_30px_rgba(0,0,0,0.2)] overflow-hidden">
        
        <!-- Customer / Member Selection -->
        <div class="p-4 border-b border-slate-700 bg-slate-800/80">
            @if($selectedMember && is_array($selectedMember))
            <div class="flex items-center justify-between bg-blue-900/20 border border-blue-500/30 p-3 rounded-xl">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-800/50 flex items-center justify-center text-blue-300 font-display font-bold">
                        {{ substr($selectedMember['name'] ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-100 flex items-center gap-2">
                            {{ $selectedMember['name'] ?? 'Member' }}
                            @if(($selectedMember['tier'] ?? '') == 'gold')
                                <span class="bg-amber-500 text-white text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider">Gold</span>
                            @elseif(($selectedMember['tier'] ?? '') == 'silver')
                                <span class="bg-slate-400 text-white text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider">Silver</span>
                            @endif
                        </div>
                        <div class="text-[11px] text-slate-400">{{ $selectedMember['phone'] ?? '-' }} • {{ $selectedMember['total_points'] ?? 0 }} Pts</div>
                    </div>
                </div>
                <button wire:click="removeMember" class="p-1.5 text-slate-400 hover:text-red-400 hover:bg-red-500/10 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            @else
            <div class="flex gap-2">
                <button wire:click="$set('showMemberModal', true)" class="flex-1 py-3 px-4 bg-slate-700 hover:bg-slate-600 border border-slate-600 rounded-xl text-slate-300 text-sm font-medium flex items-center justify-center gap-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                    Pilih / Tambah Member
                </button>
            </div>
            @endif
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-2 space-y-1">
            @forelse($cart as $key => $item)
            <div class="bg-slate-900/50 hover:bg-slate-700/50 p-3 rounded-xl group transition-colors">
                <div class="flex justify-between items-start mb-2">
                    <div class="pr-2 overflow-hidden flex-1">
                        <h4 class="text-sm font-medium text-slate-200 line-clamp-1 break-all">{{ $item['name'] ?? '' }}</h4>
                        <div class="text-[11px] text-slate-400 font-mono">Rp {{ number_format($item['price'] ?? 0, 0, ',', '.') }} / {{ $item['unit'] ?? '' }}</div>
                    </div>
                    <div class="font-mono font-bold text-slate-100 text-sm shrink-0">
                        Rp {{ number_format($item['subtotal'] ?? 0, 0, ',', '.') }}
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center bg-slate-800 rounded-lg border border-slate-700 overflow-hidden">
                        <button wire:click="updateQty('{{ $key }}', {{ ($item['qty'] ?? 1) - 1 }})" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                        </button>
                        <input type="number" wire:model.lazy="cart.{{ $key }}.qty" wire:change="updateQty('{{ $key }}', $event.target.value)" class="w-10 h-8 bg-transparent border-none text-center text-sm font-bold text-white focus:ring-0 p-0" min="1">
                        <button wire:click="updateQty('{{ $key }}', {{ ($item['qty'] ?? 1) + 1 }})" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-white hover:bg-slate-700 transition-colors">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        </button>
                    </div>
                    
                    <button wire:click="removeFromCart('{{ $key }}')" class="p-2 text-slate-500 hover:text-red-400 transition-colors rounded-lg hover:bg-red-500/10 opacity-100 lg:opacity-0 lg:group-hover:opacity-100">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            </div>
            @empty
            <div class="h-full flex flex-col items-center justify-center text-slate-500 opacity-50">
                <svg class="w-20 h-20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <p class="font-medium">Cart kosong</p>
                <p class="text-xs mt-1">Pilih produk dari grid kiri</p>
            </div>
            @endforelse
        </div>

        <!-- Notes & Action -->
        @if(count($cart) > 0)
        <div class="px-4 py-2 border-t border-slate-700 bg-slate-800">
            <div class="flex gap-2">
                <button wire:click="clearCart" class="px-3 py-2 text-red-400 bg-red-500/10 hover:bg-red-500/20 rounded-lg text-xs font-medium transition-colors">
                    Clear
                </button>
                <button wire:click="$set('showPendingModal', true)" class="flex-1 px-3 py-2 text-slate-300 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs font-medium transition-colors text-center">
                    Simpan (Pending)
                </button>
            </div>
        </div>
        @endif

        <!-- Summary & Checkout Area -->
        <div class="p-4 bg-slate-900 border-t border-slate-800 shrink-0">
            
            <!-- Voucher Input -->
            <div class="flex gap-2 mb-4">
                <input type="text" wire:model.defer="voucherCode" placeholder="Kode Voucher..." class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-sm text-white focus:ring-blue-500 uppercase placeholder-slate-500">
                <button wire:click="applyVoucher" class="px-3 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-sm font-medium transition-colors">Apply</button>
            </div>

            <!-- Calculations -->
            <div class="space-y-2 mb-4 text-sm">
                <div class="flex justify-between text-slate-400">
                    <span>Subtotal</span>
                    <span class="font-mono">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($this->memberDiscount > 0)
                <div class="flex justify-between text-emerald-400">
                    <span>Diskon Member ({{ $selectedMember['tier'] ?? '' }})</span>
                    <span class="font-mono">- Rp {{ number_format($this->memberDiscount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                @if($this->voucherDiscount > 0)
                <div class="flex justify-between text-emerald-400">
                    <span class="flex items-center gap-1">
                        Diskon Voucher
                        <button wire:click="removeVoucher" class="text-red-400 hover:text-red-300">×</button>
                    </span>
                    <span class="font-mono">- Rp {{ number_format($this->voucherDiscount, 0, ',', '.') }}</span>
                </div>
                @endif

                @if($this->taxAmount > 0)
                <div class="flex justify-between text-slate-400">
                    <span>Pajak ({{ $currentStore ? $currentStore->tax_rate : 11 }}%)</span>
                    <span class="font-mono">Rp {{ number_format($this->taxAmount, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <!-- Grand Total -->
            <div class="flex justify-between items-end mb-4 pt-4 border-t border-slate-800">
                <span class="text-slate-400 font-medium">Total</span>
                <span class="text-3xl font-display font-bold text-blue-400 font-mono tracking-tight">
                    Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                </span>
            </div>

            <!-- Pay Button -->
            <button wire:click="openPayment" @if(empty($cart)) disabled @endif class="w-full py-4 bg-blue-600 hover:bg-blue-500 disabled:bg-slate-700 disabled:text-slate-500 disabled:cursor-not-allowed text-white rounded-xl font-bold text-lg shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                BAYAR SEKARANG
            </button>
        </div>
    </div>

    <!-- MODALS -->

    <!-- Member Modal -->
    @if($showMemberModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-slate-700 flex justify-between items-center bg-slate-900/50">
                <h3 class="font-display font-bold text-lg text-white">Member / Pelanggan</h3>
                <button wire:click="$set('showMemberModal', false)" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-4 sm:p-6 overflow-y-auto flex-1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Search Existing -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-medium text-slate-300 mb-3 uppercase tracking-wider">Cari Member</h4>
                        <input type="text" wire:model.live.debounce.300ms="memberSearch" placeholder="Nama, No HP, Kode..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-4 py-2.5 text-white focus:ring-blue-500 mb-4">
                        
                        <div class="space-y-2 max-h-[250px] overflow-y-auto no-scrollbar">
                            @forelse($memberResults ?? [] as $m)
                            <div wire:click="selectMember({{ $m->id }})" class="p-3 bg-slate-900/50 hover:bg-blue-900/30 border border-slate-700 hover:border-blue-500/50 rounded-xl cursor-pointer transition-colors flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400 shrink-0">
                                    {{ substr($m->name, 0, 1) }}
                                </div>
                                <div class="overflow-hidden">
                                    <div class="font-medium text-slate-200 text-sm truncate">{{ $m->name }}</div>
                                    <div class="text-xs text-slate-500 truncate">{{ $m->phone ?? '' }} • {{ $m->tier ?? 'regular' }}</div>
                                </div>
                            </div>
                            @empty
                                @if($memberSearch)
                                <div class="text-center py-4 text-slate-500 text-sm">Tidak ditemukan.</div>
                                @endif
                            @endforelse
                        </div>
                    </div>
                    
                    <!-- Create New -->
                    <div class="border-t md:border-t-0 md:border-l border-slate-700 pt-6 md:pt-0 md:pl-6 space-y-3">
                        <h4 class="text-sm font-medium text-slate-300 mb-3 uppercase tracking-wider">Daftar Member Baru</h4>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-xs text-slate-400 mb-1">Nama Lengkap *</label>
                                <input type="text" wire:model.defer="newMemberName" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1">No. WhatsApp</label>
                                <input type="text" wire:model.defer="newMemberPhone" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-400 mb-1">Email</label>
                                <input type="email" wire:model.defer="newMemberEmail" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-white focus:ring-blue-500 text-sm">
                            </div>
                            <button wire:click="createMember" @if(!$newMemberName) disabled @endif class="w-full mt-2 py-2.5 bg-slate-700 hover:bg-slate-600 disabled:opacity-50 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors text-sm">
                                Simpan & Pilih
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Payment Modal -->
    @if($showPaymentModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl flex flex-col max-h-[95vh]">
            <div class="p-4 sm:p-5 border-b border-slate-700 flex justify-between items-center bg-slate-900 shrink-0">
                <h3 class="font-display font-bold text-xl text-white flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    Pembayaran
                </h3>
                <button wire:click="$set('showPaymentModal', false)" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-4 sm:p-6 overflow-y-auto flex-1">
                <!-- Total Amount Banner -->
                <div class="bg-blue-900/20 border border-blue-500/30 rounded-xl p-5 sm:p-6 text-center mb-6">
                    <div class="text-blue-300 text-sm font-medium mb-1 uppercase tracking-wider">Total Pembayaran</div>
                    <div class="text-3xl sm:text-4xl font-display font-bold text-white font-mono">
                        Rp {{ number_format($this->grandTotal, 0, ',', '.') }}
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <button wire:click="$set('paymentMethod', 'cash')" class="p-3 sm:p-4 rounded-xl border flex flex-col items-center gap-2 transition-all {{ $paymentMethod === 'cash' ? 'bg-blue-600/20 border-blue-500 text-blue-400' : 'bg-slate-900 border-slate-700 text-slate-400 hover:border-slate-500' }}">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        <span class="font-medium text-xs sm:text-sm">Tunai (Cash)</span>
                    </button>
                    <button wire:click="generateQris" class="p-3 sm:p-4 rounded-xl border flex flex-col items-center gap-2 transition-all {{ $paymentMethod === 'qris' ? 'bg-blue-600/20 border-blue-500 text-blue-400' : 'bg-slate-900 border-slate-700 text-slate-400 hover:border-slate-500' }}">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        <span class="font-medium text-xs sm:text-sm">QRIS / e-Wallet</span>
                    </button>
                </div>

                <!-- Dynamic Input based on Method -->
                @if($paymentMethod === 'cash')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Uang Diterima</label>
                        <div class="relative">
                            <span class="absolute left-4 top-4 text-slate-400 font-medium">Rp</span>
                            <input type="number" wire:model.live="cashReceived" class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-12 pr-4 py-4 text-2xl font-mono font-bold text-white focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Quick cash buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        @foreach([10000, 20000, 50000, 100000] as $amount)
                        <button wire:click="$set('cashReceived', {{ $amount }})" class="py-2 bg-slate-700 hover:bg-slate-600 rounded-lg text-xs font-medium text-slate-300 transition-colors">
                            {{ number_format($amount/1000, 0) }}K
                        </button>
                        @endforeach
                    </div>

                    <div class="flex justify-between items-center p-4 bg-slate-900 rounded-xl border border-slate-700">
                        <span class="text-slate-400 font-medium">Kembalian</span>
                        <span class="text-2xl font-display font-bold {{ $this->change >= 0 ? 'text-emerald-400' : 'text-red-400' }} font-mono">
                            Rp {{ number_format($this->change, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @elseif($paymentMethod === 'qris')
                <div class="space-y-4" @if($qrisImageUrl && !$qrisPaid && $qrisStatus === 'pending') wire:poll.3s="checkQrisStatus" @endif>
                    
                    {{-- Loading State --}}
                    @if($qrisLoading)
                    <div class="flex flex-col items-center justify-center py-10">
                        <div class="w-12 h-12 border-4 border-blue-500 border-t-transparent rounded-full animate-spin mb-4"></div>
                        <p class="text-slate-400 text-sm font-medium">Membuat QR Code QRIS...</p>
                        <p class="text-slate-500 text-xs mt-1">Menghubungi Midtrans Payment Gateway</p>
                    </div>
                    
                    {{-- QRIS Paid / Success State --}}
                    @elseif($qrisPaid)
                    <div class="flex flex-col items-center py-6">
                        <div class="w-20 h-20 bg-emerald-500/20 rounded-full flex items-center justify-center mb-4 animate-bounce">
                            <svg class="w-10 h-10 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h4 class="text-emerald-400 font-display font-bold text-xl mb-1">Pembayaran Diterima!</h4>
                        <p class="text-slate-400 text-sm">Transaksi QRIS berhasil dikonfirmasi</p>
                        @if($qrisReference)
                        <div class="mt-3 px-4 py-2 bg-slate-900 rounded-lg border border-slate-700">
                            <span class="text-slate-500 text-xs">Ref ID:</span>
                            <span class="text-slate-300 text-xs font-mono ml-1">{{ $qrisReference }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- QR Code Display --}}
                    @elseif($qrisImageUrl)
                    <div class="flex flex-col items-center">
                        {{-- QR Code Image from Midtrans --}}
                        <div class="bg-white p-4 rounded-xl shadow-lg mb-4">
                            <img src="{{ $qrisImageUrl }}" alt="QRIS QR Code" class="w-52 h-52 sm:w-56 sm:h-56 object-contain">
                        </div>

                        {{-- Scan instruction --}}
                        <div class="text-center mb-3">
                            <p class="text-slate-300 text-sm font-medium">Scan QR Code dengan aplikasi e-Wallet</p>
                            <p class="text-slate-500 text-xs mt-1">GoPay, OVO, DANA, ShopeePay, LinkAja, dll.</p>
                        </div>

                        {{-- Status indicator --}}
                        <div class="flex items-center gap-2 px-4 py-2 {{ $qrisStatus === 'local_pending' ? 'bg-blue-500/10 border-blue-500/30' : 'bg-amber-500/10 border-amber-500/30' }} border rounded-lg mb-3">
                            <div class="w-2.5 h-2.5 rounded-full {{ $qrisStatus === 'local_pending' ? 'bg-blue-400 animate-pulse' : 'bg-amber-400 animate-pulse' }}"></div>
                            <span class="{{ $qrisStatus === 'local_pending' ? 'text-blue-300' : 'text-amber-300' }} text-sm font-medium">
                                {{ $qrisStatus === 'local_pending' ? 'Menunggu Scan & Konfirmasi Manual' : 'Menunggu pembayaran...' }}
                            </span>
                        </div>

                        {{-- Expiry info --}}
                        @if($qrisExpiryTime)
                        <p class="text-slate-500 text-xs">Berlaku sampai: {{ $qrisExpiryTime }}</p>
                        @endif

                        @if($qrisStatus === 'local_pending')
                        <div class="w-full mt-3">
                            <button wire:click="confirmManualQris" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-bold shadow-[0_0_15px_rgba(16,185,129,0.3)] transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Konfirmasi Pembayaran Manual
                            </button>
                        </div>
                        @endif

                        {{-- Cancel / Regenerate --}}
                        <div class="flex gap-2 mt-4 w-full">
                            <button wire:click="cancelQris" class="flex-1 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-300 rounded-lg text-sm font-medium transition-colors">
                                Batalkan
                            </button>
                            <button wire:click="generateQris" class="flex-1 py-2.5 bg-blue-600/20 hover:bg-blue-600/30 text-blue-400 border border-blue-500/30 rounded-lg text-sm font-medium transition-colors">
                                Generate Ulang
                            </button>
                        </div>
                    </div>

                    {{-- Initial State / Error (no QR yet) --}}
                    @else
                    <div class="flex flex-col items-center py-6">
                        <div class="w-48 h-48 bg-slate-900 border-2 border-dashed border-slate-600 rounded-xl flex flex-col items-center justify-center text-slate-500 mb-4">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm14 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                            <span class="text-sm">QR Code akan muncul di sini</span>
                        </div>
                        <button wire:click="generateQris" class="px-6 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition-all active:scale-[0.98]">
                            Generate QRIS
                        </button>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <div class="p-4 sm:p-5 border-t border-slate-700 bg-slate-900 flex gap-3 shrink-0">
                <button wire:click="{{ $paymentMethod === 'qris' && $qrisOrderId && !$qrisPaid ? 'cancelQris' : '$set(\'showPaymentModal\', false)' }}" class="px-4 sm:px-6 py-3 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl font-medium transition-colors text-sm sm:text-base">Batal</button>
                @if($paymentMethod === 'qris')
                    <button wire:click="processPayment" 
                            @if(!$qrisPaid) disabled @endif
                            class="flex-1 py-3 rounded-xl font-bold text-base sm:text-lg transition-all active:scale-[0.98] {{ $qrisPaid ? 'bg-emerald-600 hover:bg-emerald-500 text-white shadow-[0_0_20px_rgba(16,185,129,0.3)]' : 'bg-slate-700 text-slate-500 cursor-not-allowed' }}">
                        @if($qrisPaid)
                            ✓ Selesaikan Transaksi
                        @elseif($qrisImageUrl)
                            Menunggu Pembayaran...
                        @else
                            Selesaikan Transaksi
                        @endif
                    </button>
                @else
                    <button wire:click="processPayment" class="flex-1 py-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-base sm:text-lg shadow-[0_0_20px_rgba(37,99,235,0.3)] transition-all active:scale-[0.98]">
                        Selesaikan Transaksi
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Receipt / Struk Modal -->
    @if($showReceiptModal && isset($lastTransaction) && $lastTransaction)
    <div class="fixed inset-0 z-[70] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm overflow-y-auto">
        <div class="flex flex-col gap-4 max-w-sm w-full max-h-[95vh] my-auto">
            <!-- Thermal Receipt Preview -->
            <div class="bg-white text-black p-6 rounded shadow-2xl font-mono text-xs w-full mx-auto overflow-y-auto max-h-[70vh] print:max-h-none print:overflow-visible print:shadow-none print:p-0" id="receiptArea">
                <div class="text-center mb-4">
                    <h2 class="font-bold text-base">{{ $currentStore && $currentStore->name ? $currentStore->name : 'ERP STORE' }}</h2>
                    <p>{{ $currentStore && $currentStore->address ? $currentStore->address : 'Jl. Example Address No. 123' }}</p>
                    <p>{{ $currentStore && $currentStore->phone ? $currentStore->phone : '08123456789' }}</p>
                </div>
                
                <div class="border-b border-dashed border-gray-400 pb-2 mb-2">
                    <div class="flex justify-between"><span>No:</span> <span>{{ $lastTransaction->transaction_number ?? '' }}</span></div>
                    <div class="flex justify-between"><span>Tgl:</span> <span>{{ isset($lastTransaction->created_at) ? $lastTransaction->created_at->format('d/m/Y H:i') : '' }}</span></div>
                    <div class="flex justify-between"><span>Kasir:</span> <span>{{ Auth::user()->name ?? '' }}</span></div>
                    @if(isset($lastTransaction->member) && $lastTransaction->member)
                    <div class="flex justify-between"><span>Member:</span> <span>{{ $lastTransaction->member->name ?? '' }}</span></div>
                    @endif
                </div>

                <div class="border-b border-dashed border-gray-400 pb-2 mb-2 space-y-1">
                    @if(isset($lastTransaction->items) && count($lastTransaction->items) > 0)
                        @foreach($lastTransaction->items as $item)
                        <div>
                            <div class="font-medium">{{ $item->product_name ?? '' }}</div>
                            <div class="flex justify-between">
                                <span>{{ ($item->quantity ?? 0) + 0 }} {{ $item->unit ?? '' }} x {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</span>
                                <span>{{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @endforeach
                    @endif
                </div>

                <div class="border-b border-dashed border-gray-400 pb-2 mb-2 space-y-1">
                    <div class="flex justify-between"><span>Subtotal:</span> <span>{{ number_format($lastTransaction->subtotal ?? 0, 0, ',', '.') }}</span></div>
                    @if(($lastTransaction->discount_amount ?? 0) > 0)
                    <div class="flex justify-between"><span>Diskon:</span> <span>-{{ number_format($lastTransaction->discount_amount, 0, ',', '.') }}</span></div>
                    @endif
                    @if(($lastTransaction->tax_amount ?? 0) > 0)
                    <div class="flex justify-between"><span>Pajak:</span> <span>{{ number_format($lastTransaction->tax_amount, 0, ',', '.') }}</span></div>
                    @endif
                    <div class="flex justify-between font-bold text-sm mt-1"><span>TOTAL:</span> <span>Rp {{ number_format($lastTransaction->grand_total ?? 0, 0, ',', '.') }}</span></div>
                </div>

                <div class="border-b border-dashed border-gray-400 pb-2 mb-4 space-y-1">
                    <div class="flex justify-between"><span>Bayar ({{ strtoupper($lastTransaction->payment_method ?? '') }}):</span> <span>{{ number_format($lastTransaction->cash_received ?? 0, 0, ',', '.') }}</span></div>
                    <div class="flex justify-between"><span>Kembali:</span> <span>{{ number_format($lastTransaction->cash_change ?? 0, 0, ',', '.') }}</span></div>
                </div>

                <div class="text-center mt-6">
                    <p>{{ $currentStore && $currentStore->receipt_footer ? $currentStore->receipt_footer : 'Terima Kasih Atas Kunjungan Anda!' }}</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 mt-2">
                <button onclick="window.print()" class="flex-1 py-3 bg-white text-slate-900 rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-slate-200">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    Cetak Struk
                </button>
                <button wire:click="$set('showReceiptModal', false)" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold flex items-center justify-center gap-2 hover:bg-blue-500">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
    <style>
        @media print {
            body * { visibility: hidden; }
            #receiptArea, #receiptArea * { visibility: visible; }
            #receiptArea { position: absolute; left: 0; top: 0; width: 80mm; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
    @endif

    <!-- Pending Orders Modal -->
    @if($showPendingModal)
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-slate-800 border border-slate-700 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-4 border-b border-slate-700 flex justify-between items-center bg-slate-900 shrink-0">
                <h3 class="font-display font-bold text-lg text-white">Pesanan Tertunda (Pending Orders)</h3>
                <button wire:click="$set('showPendingModal', false)" class="text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <div class="p-4 sm:p-6 overflow-y-auto bg-slate-900/50 flex-1">
                @if(count($cart) > 0)
                <div class="mb-6 p-4 bg-slate-800 border border-slate-700 rounded-xl">
                    <h4 class="text-sm font-medium text-slate-300 mb-3 uppercase tracking-wider">Simpan Cart Saat Ini</h4>
                    <div class="flex gap-3">
                        <input type="text" wire:model.defer="pendingOrderName" placeholder="Nama referensi (mis: Meja 5, Bpk Andi)..." class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-4 py-2 text-white focus:ring-blue-500 text-sm">
                        <button wire:click="savePendingOrder" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-sm font-medium transition-colors">
                            Simpan ke Pending
                        </button>
                    </div>
                </div>
                @endif

                <h4 class="text-sm font-medium text-slate-300 mb-3 uppercase tracking-wider">Daftar Order Tersimpan</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @forelse($pendingOrders ?? [] as $order)
                    <div class="bg-slate-800 border border-slate-700 rounded-xl p-4 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h5 class="font-bold text-slate-200">{{ $order->order_name ?? 'Untitled' }}</h5>
                                <p class="text-[11px] text-slate-400">{{ isset($order->created_at) ? $order->created_at->format('d M Y - H:i') : '' }} • Kasir: {{ $order->user->name ?? '' }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/20 text-amber-400 uppercase">Pending</span>
                        </div>
                        
                        <div class="text-xs text-slate-400 mb-4 line-clamp-2">
                            Items: 
                            @php
                                $cartData = is_array($order->cart_data ?? null)
                                    ? $order->cart_data
                                    : json_decode($order->cart_data ?? '[]', true);
                                $itemNames = is_array($cartData) ? array_column($cartData, 'name') : [];
                                echo e(implode(', ', array_slice($itemNames, 0, 3)));
                                if(count($itemNames) > 3) echo '...';
                            @endphp
                        </div>
                        
                        <div class="mt-auto flex gap-2">
                            <button wire:click="loadPendingOrder({{ $order->id }})" class="flex-1 py-2 bg-blue-600/20 hover:bg-blue-600/40 text-blue-400 rounded-lg text-xs font-bold transition-colors border border-blue-500/30">
                                Lanjutkan Transaksi
                            </button>
                            <button wire:click="deletePendingOrder({{ $order->id }})" onclick="confirm('Hapus order tertunda ini?') || event.stopImmediatePropagation()" class="p-2 bg-slate-700 hover:bg-red-500/20 text-slate-400 hover:text-red-400 rounded-lg transition-colors border border-slate-600 hover:border-red-500/30">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-8 text-center text-slate-500 text-sm">
                        Tidak ada pesanan tertunda saat ini.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- NOTIFICATION TOAST -->
    @if($showNotification)
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 3000); $wire.hideNotification()"
         x-show="show"
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed bottom-4 right-4 z-[80] w-96">
        
        <div class="rounded-lg shadow-2xl overflow-hidden {{ $notificationType === 'success' ? 'bg-emerald-500' : ($notificationType === 'error' ? 'bg-red-500' : 'bg-blue-500') }}">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        @if($notificationType === 'success')
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        @elseif($notificationType === 'error')
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        @else
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        @endif
                    </div>
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium text-white">
                            {{ $notificationMessage }}
                        </p>
                        @if($notificationDetails)
                        <p class="mt-1 text-xs text-white/80">
                            {{ $notificationDetails }}
                        </p>
                        @endif
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="show = false; $wire.hideNotification()" class="inline-flex text-white/80 hover:text-white focus:outline-none">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="h-1 bg-white/30">
                <div x-data="{ width: 100 }" 
                     x-init="let interval = setInterval(() => { width -= 100/30; if (width <= 0) clearInterval(interval); }, 100)"
                     :style="`width: ${width}%`"
                     class="h-full {{ $notificationType === 'success' ? 'bg-white' : ($notificationType === 'error' ? 'bg-white' : 'bg-white') }}">
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Floating Cart Banner for Mobile (Visible only on Products tab when cart has items) -->
    <div x-show="activeTab === 'products' && cartCount > 0" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100"
         x-transition:leave-end="translate-y-full opacity-0"
         class="lg:hidden fixed bottom-20 left-4 right-4 z-30 bg-blue-600 hover:bg-blue-500 text-white px-4 py-3 rounded-xl shadow-lg flex items-center justify-between cursor-pointer transition-all active:scale-[0.98] border border-blue-500"
         @click="activeTab = 'cart'" x-cloak>
        <div class="flex items-center gap-2">
            <span class="bg-white text-blue-600 font-bold px-2 py-0.5 rounded-full text-xs animate-pulse" x-text="cartCount"></span>
            <span class="text-sm font-semibold">Item di keranjang</span>
        </div>
        <div class="flex items-center gap-1 font-bold text-sm">
            <span>Lihat Keranjang & Bayar</span>
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </div>
    </div>

    <!-- Audio for success notification -->
    <audio id="successSound" preload="auto" style="display: none;">
        <source src="{{ asset('sounds/success.wav') }}" type="audio/wav">
        <source src="{{ asset('sounds/success.mp3') }}" type="audio/mpeg">
    </audio>
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('play-success-sound', () => {
            const audio = document.getElementById('successSound');
            if (audio) {
                audio.play().catch(e => console.log('Audio play failed:', e));
            }
        });
        
        Livewire.on('auto-hide-notification', () => {
            setTimeout(() => {
                Livewire.dispatch('hideNotification');
            }, 3000);
        });
    });
</script>