<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Dashboard</h1>
        <div class="text-sm text-gray-500 font-mono">
            {{ now()->format('l, d F Y') }}
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         DATE FILTER DROPDOWN
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-3">

            {{-- Dropdown Trigger --}}
            <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                <button
                    @click="open = !open"
                    class="flex items-center gap-2 pl-3 pr-2.5 py-2 bg-white border border-gray-200 hover:border-blue-400 rounded-xl text-sm font-semibold text-gray-700 shadow-sm transition-all duration-150 min-w-[220px]"
                >
                    <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="flex-1 text-left text-blue-700 font-bold truncate">{{ $this->dateLabel }}</span>
                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                {{-- Dropdown Panel --}}
                <div
                    x-show="open"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute left-0 top-full mt-2 z-50 w-64 bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden"
                    style="display: none;"
                >
                    @php
                    $groups = [
                        'Quick' => [
                            'today'        => 'Today',
                            'yesterday'    => 'Yesterday',
                            'last_7_days'  => 'Last 7 Days',
                            'last_30_days' => 'Last 30 Days',
                        ],
                        'Monthly' => [
                            'this_month'    => 'This Month',
                            'last_month'    => 'Last Month',
                            'this_month_ly' => 'This Month Last Year',
                        ],
                        'Annual' => [
                            'this_year' => 'This Year',
                            'last_year' => 'Last Year',
                        ],
                        'Financial Year' => [
                            'current_fy' => 'Current Financial Year',
                            'last_fy'    => 'Last Financial Year',
                        ],
                    ];
                    @endphp

                    <div class="py-2 max-h-80 overflow-y-auto">
                        @foreach($groups as $groupLabel => $items)
                        <div class="px-3 pt-2 pb-1">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $groupLabel }}</p>
                        </div>
                        @foreach($items as $key => $label)
                        <button
                            wire:click="applyPreset('{{ $key }}')"
                            @click="open = false"
                            class="w-full flex items-center justify-between px-4 py-2 text-sm text-left transition-colors duration-100
                                {{ $datePreset === $key
                                    ? 'bg-blue-50 text-blue-700 font-semibold'
                                    : 'text-gray-700 hover:bg-gray-50' }}"
                        >
                            <span>{{ $label }}</span>
                            @if($datePreset === $key)
                            <svg class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            @endif
                        </button>
                        @endforeach
                        @endforeach

                        {{-- Divider --}}
                        <div class="border-t border-gray-100 my-2"></div>

                        {{-- Custom Range Option --}}
                        <button
                            wire:click="applyPreset('custom')"
                            @click="open = false"
                            class="w-full flex items-center gap-2 px-4 py-2 text-sm text-left transition-colors duration-100
                                {{ $datePreset === 'custom'
                                    ? 'bg-blue-50 text-blue-700 font-semibold'
                                    : 'text-gray-700 hover:bg-gray-50' }}"
                        >
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            Custom Range...
                            @if($datePreset === 'custom')
                            <svg class="w-4 h-4 text-blue-600 shrink-0 ml-auto" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            @endif
                        </button>
                    </div>
                </div>
            </div>

            {{-- Active period badge (shown next to dropdown) --}}
            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                <span class="text-gray-400">Showing:</span>
                <span class="font-semibold text-gray-700">
                    {{ $dateStart->format('d M Y') }} — {{ $dateEnd->format('d M Y') }}
                </span>
            </div>
        </div>

        {{-- Custom Date Range Picker --}}
        @if($showDatePicker || $datePreset === 'custom')
        <div class="mt-4 pt-4 border-t border-gray-100">
            <div class="flex flex-wrap items-end gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">Start Date</label>
                    <input
                        type="date"
                        wire:model="customStart"
                        class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                    >
                </div>
                <div class="text-gray-400 font-bold pb-2">→</div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5 uppercase tracking-wider">End Date</label>
                    <input
                        type="date"
                        wire:model="customEnd"
                        class="bg-gray-50 border border-gray-200 rounded-lg px-3 py-2 text-sm text-gray-800 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none"
                    >
                </div>
                <button
                    wire:click="applyCustomRange"
                    class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-bold shadow-sm shadow-blue-200 transition-colors"
                >
                    Apply Range
                </button>
            </div>
            @if($datePreset === 'custom' && $customStart && $customEnd)
            <p class="mt-2 text-xs text-gray-400 font-mono">
                Showing data from {{ \Carbon\Carbon::parse($customStart)->format('d M Y') }} to {{ \Carbon\Carbon::parse($customEnd)->format('d M Y') }}
            </p>
            @endif
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════
         STAT CARDS
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-text-muted)]">Total Sales</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $this->dateLabel }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalSales, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500">
                <span class="text-emerald-600 font-medium">↑ 12%</span> vs previous period
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-text-muted)]">Total Purchases</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">{{ $this->dateLabel }}</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        Rp {{ number_format($totalPurchases, 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-red-50 rounded-lg">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500">
                <span class="text-gray-500 font-medium">—</span> no comparison data
            </p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-text-muted)]">Stock Items On Hand</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">Current snapshot</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">
                        {{ number_format($stockCount, 0, ',', '.') }} pcs
                    </p>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500">Across {{ $warehouseCount }} warehouses</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-[var(--color-text-muted)]">Active Employees</p>
                    <p class="text-[10px] text-gray-400 font-mono mt-0.5">Current headcount</p>
                    <p class="mt-1 text-2xl font-bold text-gray-900">{{ $employeeCount }}</p>
                </div>
                <div class="p-3 bg-amber-50 rounded-lg">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
            </div>
            <p class="mt-4 text-xs text-gray-500">Headcount status: active</p>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         ATTENDANCE SECTION
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        {{-- Section Header --}}
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-blue-600 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-white font-display">Absensi Kehadiran</h2>
                    <p class="text-xs text-blue-100">{{ now()->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
            <a href="{{ route('hr') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white text-xs font-semibold rounded-lg backdrop-blur-sm transition-colors">
                Lihat Selengkapnya
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>

        {{-- Attendance Alert Messages --}}
        @if (session()->has('att_success'))
            <div class="mx-6 mt-4 p-3 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200 flex items-center gap-2" role="alert">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><strong>Berhasil!</strong> {{ session('att_success') }}</span>
            </div>
        @endif
        @if (session()->has('att_error'))
            <div class="mx-6 mt-4 p-3 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200 flex items-center gap-2" role="alert">
                <svg class="w-5 h-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span><strong>Error!</strong> {{ session('att_error') }}</span>
            </div>
        @endif

        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Clock In / Clock Out Card --}}
                <div class="bg-gradient-to-br from-gray-50 to-blue-50/50 rounded-xl border border-gray-200 p-5 flex flex-col font-sans"
                     x-data="{
                        lat: null,
                        lng: null,
                        accuracy: null,
                        errorMsg: null,
                        checking: false,
                        officeLat: {{ $attendanceSetting ? $attendanceSetting->office_latitude : 0 }},
                        officeLng: {{ $attendanceSetting ? $attendanceSetting->office_longitude : 0 }},
                        maxRadius: {{ $attendanceSetting ? $attendanceSetting->allowed_radius : 200 }},
                        distance: null,

                        init() {
                            this.getCoordinates(false);
                        },

                        calculateDistance(lat1, lon1, lat2, lon2) {
                            const R = 6371e3;
                            const phi1 = lat1 * Math.PI/180;
                            const phi2 = lat2 * Math.PI/180;
                            const deltaPhi = (lat2-lat1) * Math.PI/180;
                            const deltaLambda = (lon2-lon1) * Math.PI/180;
                            const a = Math.sin(deltaPhi/2) * Math.sin(deltaPhi/2) +
                                      Math.cos(phi1) * Math.cos(phi2) *
                                      Math.sin(deltaLambda/2) * Math.sin(deltaLambda/2);
                            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
                            return R * c;
                        },

                        getCoordinates(showNotice = true) {
                            if (!navigator.geolocation) {
                                this.errorMsg = 'Geolocation tidak didukung oleh browser Anda.';
                                return;
                            }
                            this.checking = true;
                            this.errorMsg = null;
                            navigator.geolocation.getCurrentPosition(
                                (position) => {
                                    this.lat = position.coords.latitude;
                                    this.lng = position.coords.longitude;
                                    this.accuracy = position.coords.accuracy;
                                    this.checking = false;
                                    if (this.officeLat && this.officeLng) {
                                        this.distance = this.calculateDistance(this.lat, this.lng, this.officeLat, this.officeLng);
                                    }
                                },
                                (error) => {
                                    this.checking = false;
                                    switch(error.code) {
                                        case error.PERMISSION_DENIED: this.errorMsg = 'Izin lokasi ditolak.'; break;
                                        case error.POSITION_UNAVAILABLE: this.errorMsg = 'Lokasi GPS tidak terdeteksi.'; break;
                                        case error.TIMEOUT: this.errorMsg = 'Timeout saat mendeteksi lokasi.'; break;
                                        default: this.errorMsg = 'Gagal mendeteksi lokasi.';
                                    }
                                    if(showNotice) alert(this.errorMsg);
                                },
                                { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                            );
                        }
                     }"
                >
                    {{-- Card Header --}}
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3 mb-4">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            Pencatatan Kehadiran
                        </h3>
                        <button type="button" @click="getCoordinates(true)" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                            <svg class="w-3.5 h-3.5" :class="checking ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.233"></path></svg>
                            Refresh GPS
                        </button>
                    </div>

                    {{-- Current User Info --}}
                    @if($currentEmployee)
                        <div class="p-3 bg-white rounded-lg border border-gray-200 mb-3">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $currentEmployee->name }}</div>
                                    <div class="text-[10px] text-gray-500 font-mono">{{ $currentEmployee->employee_number }}</div>
                                    <div class="text-[10px] text-gray-500">{{ $currentEmployee->position }} @if($currentEmployee->department) - {{ $currentEmployee->department }} @endif</div>
                                </div>
                                @if($myAttendance && $myAttendance->check_in)
                                    @if($myAttendance->check_out)
                                        <span class="px-2 py-1 bg-gray-500 text-white text-[9px] font-bold rounded-full uppercase">SELESAI</span>
                                    @else
                                        <span class="px-2 py-1 bg-emerald-600 text-white text-[9px] font-bold rounded-full uppercase animate-pulse">AKTIF</span>
                                    @endif
                                @else
                                    <span class="px-2 py-1 bg-gray-200 text-gray-600 text-[9px] font-bold rounded-full uppercase">BELUM ABSEN</span>
                                @endif
                            </div>
                            @if($myAttendance)
                                <div class="mt-2 pt-2 border-t border-gray-100 flex items-center gap-3 text-[10px] text-gray-600 font-mono">
                                    <span>IN: <strong class="text-emerald-700">{{ $myAttendance->check_in ? Carbon\Carbon::parse($myAttendance->check_in)->format('H:i') : '--:--' }}</strong></span>
                                    <span>OUT: <strong class="text-rose-700">{{ $myAttendance->check_out ? Carbon\Carbon::parse($myAttendance->check_out)->format('H:i') : '--:--' }}</strong></span>
                                    @if($myAttendance->check_in && $myAttendance->check_out)
                                        @php
                                            $dur = Carbon\Carbon::parse($myAttendance->check_in)->diff(Carbon\Carbon::parse($myAttendance->check_out));
                                        @endphp
                                        <span class="text-blue-600">Durasi: <strong>{{ $dur->format('%H:%I') }}</strong></span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- GPS Status --}}
                    <div class="p-3 rounded-lg border text-xs mb-4"
                         :class="errorMsg ? 'bg-red-50 border-red-200 text-red-800' : (lat ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-gray-50 border-gray-200 text-gray-600')">
                        <div class="font-bold flex items-center justify-between mb-1.5">
                            <span>Status GPS</span>
                            <span class="h-2 w-2 rounded-full" :class="errorMsg ? 'bg-red-500' : (lat ? 'bg-emerald-500' : 'bg-gray-400')"></span>
                        </div>
                        <div class="space-y-0.5 font-mono text-[10px]">
                            <template x-if="checking">
                                <div class="text-gray-500 animate-pulse">Mendeteksi koordinat GPS...</div>
                            </template>
                            <template x-if="!checking && lat">
                                <div>
                                    <div class="flex justify-between"><span>Lat:</span><span class="font-bold" x-text="lat.toFixed(6)"></span></div>
                                    <div class="flex justify-between"><span>Lng:</span><span class="font-bold" x-text="lng.toFixed(6)"></span></div>
                                    <template x-if="distance !== null">
                                        <div class="flex justify-between mt-1 pt-1 border-t border-emerald-100 font-sans text-[10px]">
                                            <span>Jarak ke kantor:</span>
                                            <span class="font-bold" :class="distance > maxRadius ? 'text-red-600' : 'text-emerald-700'" x-text="Math.round(distance) + ' m'"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!checking && errorMsg">
                                <div class="text-red-700" x-text="errorMsg"></div>
                            </template>
                            <template x-if="!checking && !lat && !errorMsg">
                                <div class="text-gray-500">Klik Refresh GPS untuk mendeteksi lokasi.</div>
                            </template>
                        </div>
                    </div>

                    {{-- Clock Buttons --}}
                    <div class="space-y-2 mt-auto">
                        @php
                            $canClockIn  = $currentEmployee && (!$myAttendance || !$myAttendance->check_in);
                            $canClockOut = $currentEmployee && $myAttendance && $myAttendance->check_in && !$myAttendance->check_out;
                        @endphp

                        <button type="button"
                            @if($canClockIn)
                                @click="if(!lat) { alert('GPS koordinat belum didapatkan!'); return; } $wire.clockIn(lat, lng)"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-sm shadow-sm transition cursor-pointer"
                            @else
                                disabled
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-300 text-gray-500 rounded-lg font-bold text-sm cursor-not-allowed"
                            @endif
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            {{ $canClockIn ? 'CLOCK IN (Masuk)' : ($myAttendance && $myAttendance->check_in ? '✓ Sudah Clock In' : 'CLOCK IN (Masuk)') }}
                        </button>

                        <button type="button"
                            @if($canClockOut)
                                @click="if(!lat) { alert('GPS koordinat belum didapatkan!'); return; } $wire.clockOut(lat, lng)"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-sm shadow-sm transition cursor-pointer"
                            @else
                                disabled
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 bg-gray-300 text-gray-500 rounded-lg font-bold text-sm cursor-not-allowed"
                            @endif
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h1a3 3 0 013 3v1"></path></svg>
                            {{ $canClockOut ? 'CLOCK OUT (Pulang)' : ($myAttendance && $myAttendance->check_out ? '✓ Sudah Clock Out' : 'CLOCK OUT (Pulang)') }}
                        </button>
                    </div>
                </div>

                {{-- Today's Attendance Stats + Recent Log --}}
                <div class="lg:col-span-2 space-y-5">
                    {{-- Stats Row --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="bg-white rounded-xl border border-gray-200 p-4 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-blue-50 rounded-full mb-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="text-2xl font-bold text-gray-900 font-mono">{{ $attTotal }}</div>
                            <div class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-0.5">Total Hadir</div>
                        </div>
                        <div class="bg-white rounded-xl border border-emerald-200 p-4 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-emerald-50 rounded-full mb-2">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="text-2xl font-bold text-emerald-700 font-mono">{{ $attPresent }}</div>
                            <div class="text-[10px] font-semibold text-emerald-600 uppercase tracking-wider mt-0.5">Tepat Waktu</div>
                        </div>
                        <div class="bg-white rounded-xl border border-amber-200 p-4 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-amber-50 rounded-full mb-2">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="text-2xl font-bold text-amber-700 font-mono">{{ $attLate }}</div>
                            <div class="text-[10px] font-semibold text-amber-600 uppercase tracking-wider mt-0.5">Terlambat</div>
                        </div>
                        <div class="bg-white rounded-xl border border-red-200 p-4 text-center">
                            <div class="inline-flex items-center justify-center w-10 h-10 bg-red-50 rounded-full mb-2">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <div class="text-2xl font-bold text-red-700 font-mono">{{ $attAbsent }}</div>
                            <div class="text-[10px] font-semibold text-red-600 uppercase tracking-wider mt-0.5">Belum Absen</div>
                        </div>
                    </div>

                    {{-- Attendance Settings Banner --}}
                    @if ($attendanceSetting)
                        <div class="flex flex-wrap items-center gap-3 px-4 py-2.5 bg-blue-50/70 rounded-lg border border-blue-100 text-xs text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <span class="font-semibold text-gray-800">{{ $attendanceSetting->office_name }}</span>
                            </div>
                            <span class="text-gray-400">|</span>
                            <span>Radius: <strong>{{ $attendanceSetting->allowed_radius }}m</strong></span>
                            <span class="text-gray-400">|</span>
                            <span>Jam Kerja: <strong>{{ $attendanceSetting->work_start_time }} - {{ $attendanceSetting->work_end_time }}</strong></span>
                            <span class="text-gray-400">|</span>
                            <span>Toleransi: <strong>{{ $attendanceSetting->late_tolerance_minutes }} menit</strong></span>
                        </div>
                    @endif

                    {{-- Recent Attendance Log --}}
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Log Kehadiran Hari Ini</span>
                            <span class="text-[10px] text-gray-400 font-mono">{{ now()->format('d/m/Y') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 text-xs">
                                <thead>
                                    <tr class="bg-gray-50/50 text-left font-semibold text-gray-500">
                                        <th class="py-2.5 px-4">Karyawan</th>
                                        <th class="py-2.5 px-4">Clock In</th>
                                        <th class="py-2.5 px-4">Clock Out</th>
                                        <th class="py-2.5 px-4 text-center">Status</th>
                                        <th class="py-2.5 px-4">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($recentAttendances as $att)
                                        <tr class="hover:bg-gray-50/50 transition duration-150">
                                            <td class="py-2.5 px-4">
                                                <div class="font-semibold text-gray-900 text-xs">{{ $att->employee->name }}</div>
                                                <div class="text-[9px] text-gray-500 font-mono">{{ $att->employee->employee_number }}</div>
                                            </td>
                                            <td class="py-2.5 px-4 font-mono font-bold text-gray-900">
                                                {{ $att->check_in ? Carbon\Carbon::parse($att->check_in)->format('H:i') : '--:--' }}
                                            </td>
                                            <td class="py-2.5 px-4 font-mono font-bold text-gray-900">
                                                {{ $att->check_out ? Carbon\Carbon::parse($att->check_out)->format('H:i') : '--:--' }}
                                            </td>
                                            <td class="py-2.5 px-4 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider
                                                    {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                                    {{ $att->status === 'late' ? 'bg-amber-100 text-amber-800' : '' }}
                                                    {{ $att->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                                ">
                                                    {{ $att->status === 'present' ? 'Hadir' : ($att->status === 'late' ? 'Terlambat' : ucfirst($att->status)) }}
                                                </span>
                                            </td>
                                            <td class="py-2.5 px-4 text-gray-500 max-w-[120px] truncate" title="{{ $att->notes }}">
                                                {{ $att->notes ?: '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="py-8 text-center text-gray-400">
                                                <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Belum ada data kehadiran hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         TREND CHART + ROLE PANEL
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold font-display text-gray-900">Revenue Trend</h2>
                    <p class="text-xs text-gray-400 mt-1">{{ $this->dateLabel }}</p>
                </div>
            </div>
            
            <div class="mt-6 flex items-end justify-between gap-1 h-48 px-2 font-sans border-b border-gray-150 pb-2">
                @php
                    $maxTrend = !empty($salesTrend) ? (max(array_values($salesTrend)) ?: 1) : 1;
                @endphp
                @forelse($salesTrend as $month => $val)
                    @php
                        $heightPercent = ($val / $maxTrend) * 100;
                        $heightPercent = max($heightPercent, 4);
                    @endphp
                    <div class="flex flex-col items-center flex-1 group min-w-0">
                        <span class="text-[10px] font-bold font-mono text-blue-600 mb-1 opacity-0 group-hover:opacity-100 transition duration-150 whitespace-nowrap">
                            Rp{{ number_format($val/1000000, 1) }}M
                        </span>
                        <div
                            class="w-full bg-blue-100 rounded-t hover:bg-blue-600 transition duration-200 ease-in-out cursor-pointer"
                            style="height: {{ $heightPercent }}%;"
                        ></div>
                        <span class="text-[10px] font-semibold text-gray-500 mt-2 truncate w-full text-center">{{ $month }}</span>
                    </div>
                @empty
                    <div class="w-full flex items-center justify-center text-gray-400 text-sm">
                        No data for this period.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold font-display text-gray-900 mb-2">Role Action Panel</h2>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-4 bg-gray-50 p-2 rounded">
                <span class="font-bold text-blue-600">Assigned Profile:</span>
                <span class="uppercase font-semibold tracking-wider bg-blue-100 text-blue-800 px-2 py-0.5 rounded">{{ $userRole }}</span>
            </div>

            @if(in_array($userRole, ['admin', 'manager']))
                <div class="space-y-4 font-sans text-sm">
                    <p class="text-gray-600">Administrative clearance to override rules and evaluate approvals.</p>
                    <div class="p-4 bg-amber-50 rounded-lg border border-amber-200">
                        <span class="block text-xs font-semibold text-amber-800 uppercase">Awaiting Action</span>
                        <span class="block text-2xl font-bold text-amber-900 mt-1 font-mono">{{ $pendingApprovals }} Requests</span>
                        <a href="{{ route('approvals') }}" class="inline-flex items-center mt-3 text-xs font-bold text-amber-800 hover:text-amber-950 underline">
                            Go to Approvals Engine →
                        </a>
                    </div>
                </div>
            @elseif($userRole === 'warehouse')
                <div class="space-y-4 font-sans text-sm">
                    <p class="text-gray-600 font-medium">Warehouse Logistics Desk</p>
                    <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <span class="block text-xs font-semibold text-blue-800 uppercase">Pending Physical Deliveries</span>
                        <span class="block text-2xl font-bold text-blue-900 mt-1 font-mono">{{ $pendingDeliveries }} Delivery Orders</span>
                        <a href="{{ route('delivery-orders') }}" class="inline-flex items-center mt-3 text-xs font-bold text-blue-800 hover:text-blue-950 underline">
                            Open Shipping Desk →
                        </a>
                    </div>
                </div>
            @elseif($userRole === 'hr')
                <div class="space-y-4 font-sans text-sm">
                    <p class="text-gray-600">Rostering & Capacity Planning Desk</p>
                    <div class="p-4 bg-emerald-50 rounded-lg border border-emerald-200">
                        <span class="block text-xs font-semibold text-emerald-800 uppercase">Upcoming Assigned Rosters</span>
                        <span class="block text-2xl font-bold text-emerald-900 mt-1 font-mono">{{ $activeSchedules }} Assigned Shifts</span>
                        <a href="{{ route('schedules') }}" class="inline-flex items-center mt-3 text-xs font-bold text-emerald-800 hover:text-emerald-950 underline">
                            Roster Planning Calendars →
                        </a>
                    </div>
                </div>
            @else
                <div class="space-y-4 font-sans text-sm">
                    <p class="text-gray-600">Financial Reports & General Oversight</p>
                    <div class="p-4 bg-purple-50 rounded-lg border border-purple-200">
                        <span class="block text-xs font-semibold text-purple-800 uppercase">Executive Reports</span>
                        <span class="block text-xs text-gray-500 mt-1">Review Trial Balances, Cash Flows, and AR/AP Aging.</span>
                        <a href="{{ route('reports') }}" class="inline-flex items-center mt-3 text-xs font-bold text-purple-800 hover:text-purple-950 underline">
                            Open Executive Reports →
                        </a>
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         ADDITIONAL CHARTS: Last 30 Days & Financial Year
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Sales Last 30 Days Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold font-display text-gray-900">Sales Last 30 Days</h2>
                    <p class="text-xs text-gray-400 mt-1">Daily sales performance</p>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="mt-4">
                @php
                    $max30 = !empty($salesLast30Days) ? (max(array_values($salesLast30Days)) ?: 1) : 1;
                    $chartData30 = array_slice($salesLast30Days, -15, 15, true); // Show last 15 days for better visibility
                @endphp
                <div class="flex items-end justify-between gap-1 h-64 px-1 font-sans">
                    @forelse($chartData30 as $day => $amount)
                        @php
                            $heightPercent = ($amount / $max30) * 100;
                            $heightPercent = max($heightPercent, 4);
                            $isWeekend = in_array(Carbon\Carbon::parse($day)->dayOfWeek, [0, 6]);
                        @endphp
                        <div class="flex flex-col items-center flex-1 group min-w-0">
                            <span class="text-[9px] font-bold font-mono text-blue-600 mb-1 opacity-0 group-hover:opacity-100 transition duration-150 whitespace-nowrap">
                                Rp{{ number_format($amount/1000000, 1) }}M
                            </span>
                            <div
                                class="w-full rounded-t transition-all duration-200 ease-in-out cursor-pointer relative overflow-hidden
                                    {{ $isWeekend ? 'bg-gray-200 hover:bg-gray-300' : 'bg-blue-500 hover:bg-blue-600' }}"
                                style="height: {{ $heightPercent }}%;"
                            >
                                <div class="absolute inset-0 opacity-0 group-hover:opacity-100 bg-white/20 transition-opacity"></div>
                            </div>
                            <span class="text-[9px] font-medium text-gray-500 mt-2 truncate w-full text-center">{{ $day }}</span>
                        </div>
                    @empty
                        <div class="w-full flex items-center justify-center text-gray-400 text-sm py-12">
                            No sales data available for last 30 days
                        </div>
                    @endforelse
                </div>
                
                @if(!empty($salesLast30Days))
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Total 30 days: <strong class="text-gray-900">Rp {{ number_format(array_sum($salesLast30Days), 0, ',', '.') }}</strong></span>
                        <span>Daily avg: <strong class="text-gray-900">Rp {{ number_format(array_sum($salesLast30Days) / 30, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Sales Current Financial Year Chart --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-xl font-bold font-display text-gray-900">Sales Current Financial Year</h2>
                    <p class="text-xs text-gray-400 mt-1">
                        {{ Carbon\Carbon::now()->month >= 4 ? Carbon\Carbon::now()->year : Carbon\Carbon::now()->year - 1 }}
                        - {{ Carbon\Carbon::now()->month >= 4 ? Carbon\Carbon::now()->year + 1 : Carbon\Carbon::now()->year }}
                    </p>
                </div>
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            
            <div class="mt-4">
                @php
                    $maxFY = !empty($salesCurrentFY) ? (max(array_values($salesCurrentFY)) ?: 1) : 1;
                    $cumulativeFY = 0;
                    $cumulativeData = [];
                    foreach($salesCurrentFY as $month => $amount) {
                        $cumulativeFY += $amount;
                        $cumulativeData[$month] = $cumulativeFY;
                    }
                @endphp
                
                <!-- Monthly Bars -->
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 mb-3">Monthly Performance</p>
                    <div class="flex items-end justify-between gap-1.5 h-48 px-1 font-sans">
                        @forelse($salesCurrentFY as $month => $amount)
                            @php
                                $heightPercent = ($amount / $maxFY) * 100;
                                $heightPercent = max($heightPercent, 4);
                            @endphp
                            <div class="flex flex-col items-center flex-1 group min-w-0">
                                <span class="text-[9px] font-bold font-mono text-emerald-600 mb-1 opacity-0 group-hover:opacity-100 transition duration-150 whitespace-nowrap">
                                    Rp{{ number_format($amount/1000000, 1) }}M
                                </span>
                                <div
                                    class="w-full bg-gradient-to-t from-emerald-500 to-emerald-400 rounded-t transition-all duration-200 ease-in-out cursor-pointer hover:from-emerald-600 hover:to-emerald-500"
                                    style="height: {{ $heightPercent }}%;"
                                ></div>
                                <span class="text-[9px] font-medium text-gray-500 mt-2 truncate w-full text-center">{{ $month }}</span>
                            </div>
                        @empty
                            <div class="w-full flex items-center justify-center text-gray-400 text-sm py-12">
                                No sales data available for current financial year
                            </div>
                        @endforelse
                    </div>
                </div>
                
                <!-- Cumulative Line -->
                @if(!empty($salesCurrentFY))
                <div class="pt-3 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-500 mb-3">Cumulative YTD Performance</p>
                    @php
                        $maxCumulative = max($cumulativeData) ?: 1;
                    @endphp
                    <div class="relative h-24 mb-2">
                        <svg class="w-full h-full" viewBox="0 0 {{ count($cumulativeData) * 40 }} 100" preserveAspectRatio="none">
                            @php
                                $points = [];
                                $i = 0;
                                $totalMonths = count($cumulativeData);
                                foreach($cumulativeData as $value) {
                                    $x = ($i / ($totalMonths - 1)) * 100;
                                    $y = 100 - (($value / $maxCumulative) * 90);
                                    $points[] = "{$x},{$y}";
                                    $i++;
                                }
                            @endphp
                            @if(count($points) > 1)
                                <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                <polyline points="{{ implode(' ', $points) }}" fill="none" stroke="#10b981" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" class="opacity-20"/>
                            @endif
                        </svg>
                    </div>
                    <div class="flex justify-between text-[10px] text-gray-500">
                        @foreach(array_keys($cumulativeData) as $month)
                            <span>{{ substr($month, 0, 3) }}</span>
                        @endforeach
                    </div>
                    <div class="mt-3 pt-2 border-t border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">YTD Total:</span>
                            <strong class="text-emerald-600 text-lg">Rp {{ number_format($cumulativeFY, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         RECENT SALES + ACTIVITY LOG
    ═══════════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-bold font-display text-gray-900">Recent Sales</h2>
                <span class="text-xs text-gray-400 font-mono bg-gray-50 border border-gray-200 px-2 py-1 rounded">
                    {{ $this->dateLabel }}
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left font-semibold text-gray-500">
                            <th class="py-3 px-4">Invoice #</th>
                            <th class="py-3 px-4">Customer</th>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Amount</th>
                            <th class="py-3 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($recentSales as $sale)
                        <tr>
                            <td class="py-3 px-4 font-mono font-medium text-blue-600">{{ $sale->invoice_number }}</td>
                            <td class="py-3 px-4">{{ $sale->customer->name }}</td>
                            <td class="py-3 px-4">{{ $sale->sale_date }}</td>
                            <td class="py-3 px-4">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">
                                @php
                                $badgeClass = match($sale->status) {
                                    'draft'     => 'bg-gray-100 text-gray-700',
                                    'confirmed' => 'bg-blue-100 text-blue-700',
                                    'shipped'   => 'bg-amber-100 text-amber-700',
                                    'delivered' => 'bg-emerald-100 text-emerald-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-500',
                                };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ ucfirst($sale->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-gray-500">No sales transactions for this period.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-bold font-display text-gray-900 mb-4">System Activity Log</h2>
            <div class="space-y-4">
                @forelse ($activities as $activity)
                <div class="flex items-start">
                    <div class="flex-shrink-0 pt-0.5">
                        <span class="inline-block h-2 w-2 rounded-full bg-blue-600 mt-1.5"></span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $activity->action }}</p>
                        <p class="text-xs text-gray-500">{{ $activity->description }}</p>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $activity->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-500 text-center py-6">No recent activity logs.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>