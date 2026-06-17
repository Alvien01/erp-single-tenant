<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-[var(--color-text)]">Human Resources</h1>
            <p class="text-sm text-gray-500 mt-1">Manage employee records, log daily attendance, calculate monthly payroll, approve cuti, and manage departments.</p>
        </div>
        @if($activeTab === 'employees')
            <button wire:click="createEmployee" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Add Employee
            </button>
        @elseif($activeTab === 'attendance')
            <button wire:click="openAttendanceSettings" class="inline-flex items-center px-4 py-2 bg-slate-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-700 active:bg-slate-900 focus:outline-none transition ease-in-out duration-150 cursor-pointer">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Attendance Settings
            </button>
        @elseif($activeTab === 'payroll')
            <button wire:click="createPayroll" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Generate Payroll
            </button>
        @elseif($activeTab === 'departments')
            <button wire:click="createDepartment" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Department
            </button>
        @elseif($activeTab === 'leaves')
            <button wire:click="createLeave" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Request Cuti
            </button>
        @endif
    </div>

    <!-- Alert / Toast Messages -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 border border-red-200" role="alert">
            <span class="font-medium">Error!</span> {{ session('error') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button wire:click="$set('activeTab', 'employees')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'employees' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Employees
            </button>
            <button wire:click="$set('activeTab', 'attendance')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'attendance' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Attendance
            </button>
            <button wire:click="$set('activeTab', 'payroll')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'payroll' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Payroll Summary
            </button>
            <button wire:click="$set('activeTab', 'departments')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'departments' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Departments
            </button>
            <button wire:click="$set('activeTab', 'leaves')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === 'leaves' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Leaves Request
            </button>
        </nav>
    </div>

    @if($activeTab === 'employees')
        <!-- Search Employee -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex-1 max-w-md relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search by name, code or department...">
            </div>
        </div>

        <!-- Employee Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">ID Code</th>
                            <th class="py-3.5 px-6">Name</th>
                            <th class="py-3.5 px-6">Department</th>
                            <th class="py-3.5 px-6">Position</th>
                            <th class="py-3.5 px-6 text-right">Basic Salary</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($employees as $emp)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-mono text-blue-600 font-semibold">{{ $emp->employee_number }}</td>
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $emp->name }}</td>
                                <td class="py-4 px-6 text-gray-700">{{ $emp->department }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $emp->position }}</td>
                                <td class="py-4 px-6 text-right font-mono font-medium text-gray-900">Rp {{ number_format($emp->salary, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $emp->join_date }}</td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <button wire:click="editEmployee({{ $emp->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteEmployee({{ $emp->id }})" wire:confirm="Delete this employee?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    No employee records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'attendance')
        <!-- Geolocation & Time Settings Stats banner -->
        @if($attendanceSetting)
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 font-sans mb-6">
                <div class="flex items-center space-x-3">
                    <div class="p-2.5 bg-blue-600 rounded-lg text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm">Absensi Aktif: {{ $attendanceSetting->office_name }}</h4>
                        <p class="text-xs text-gray-600">
                            Radius: <span class="font-semibold">{{ $attendanceSetting->allowed_radius }} meter</span> | 
                            Jam Kerja: <span class="font-semibold">{{ $attendanceSetting->work_start_time }} - {{ $attendanceSetting->work_end_time }}</span> |
                            Toleransi: <span class="font-semibold">{{ $attendanceSetting->late_tolerance_minutes }} menit</span>
                        </p>
                    </div>
                </div>
                <div class="text-xs bg-white py-1 px-3 rounded-full border border-blue-200 text-blue-700 font-medium">
                    Koordinat Kantor: {{ $attendanceSetting->office_latitude }}, {{ $attendanceSetting->office_longitude }}
                </div>
            </div>
        @else
            <div class="bg-amber-50 border border-amber-200 text-amber-800 p-4 rounded-lg text-sm mb-6 flex justify-between items-center font-sans">
                <span>Pengaturan lokasi & jam kerja absensi belum dibuat.</span>
                <button wire:click="openAttendanceSettings" class="bg-amber-600 text-white px-3 py-1.5 rounded text-xs font-semibold hover:bg-amber-700 transition">Atur Sekarang</button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 font-sans">
            <!-- Clock In/Clock Out Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col justify-between" 
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
                        const R = 6371e3; // meters
                        const phi1 = lat1 * Math.PI/180;
                        const phi2 = lat2 * Math.PI/180;
                        const deltaPhi = (lat2-lat1) * Math.PI/180;
                        const deltaLambda = (lon2-lon1) * Math.PI/180;

                        const a = Math.sin(deltaPhi/2) * Math.sin(deltaPhi/2) +
                                  Math.cos(phi1) * Math.cos(phi2) *
                                  Math.sin(deltaLambda/2) * Math.sin(deltaLambda/2);
                        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

                        return R * c; // meters
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
                                    case error.PERMISSION_DENIED:
                                        this.errorMsg = 'Izin lokasi ditolak. Harap izinkan akses lokasi.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        this.errorMsg = 'Lokasi GPS tidak terdeteksi.';
                                        break;
                                    case error.TIMEOUT:
                                        this.errorMsg = 'Timeout saat mendeteksi lokasi.';
                                        break;
                                    default:
                                        this.errorMsg = 'Gagal mendeteksi lokasi.';
                                }
                                if(showNotice) {
                                    alert(this.errorMsg);
                                }
                            },
                            { enableHighAccuracy: true, timeout: 8000, maximumAge: 0 }
                        );
                    }
                 }"
            >
                <div>
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3 flex items-center justify-between">
                        <span>Pencatatan Kehadiran</span>
                        <button type="button" @click="getCoordinates(true)" class="text-xs text-blue-600 hover:underline flex items-center gap-1 font-semibold">
                            <svg class="w-3.5 h-3.5" :class="checking ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.233"></path></svg>
                            Refresh GPS
                        </button>
                    </h3>

                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Pilih Karyawan</label>
                            <select wire:model="clock_employee_id" class="mt-1.5 block w-full border border-gray-300 rounded-md py-2 px-3 text-sm bg-white focus:ring-blue-500 focus:border-blue-500">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($allEmployees as $e)
                                    <option value="{{ $e->id }}">{{ $e->employee_number }} - {{ $e->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- GPS Details Box -->
                        <div class="p-3.5 rounded-lg border text-sm" :class="errorMsg ? 'bg-red-50 border-red-200 text-red-800' : (lat ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-gray-50 border-gray-200 text-gray-600')">
                            <div class="font-bold flex items-center justify-between">
                                <span>Status Lokasi GPS</span>
                                <span class="h-2 w-2 rounded-full" :class="errorMsg ? 'bg-red-500' : (lat ? 'bg-emerald-500' : 'bg-gray-400')"></span>
                            </div>
                            <div class="mt-2 space-y-1 text-xs font-mono">
                                <template x-if="checking">
                                    <div class="text-gray-500 animate-pulse">Mendeteksi koordinat GPS Anda...</div>
                                </template>
                                <template x-if="!checking && lat">
                                    <div>
                                        <div class="flex justify-between">
                                            <span>Latitude:</span>
                                            <span class="font-bold" x-text="lat.toFixed(6)"></span>
                                        </div>
                                        <div class="flex justify-between mt-1">
                                            <span>Longitude:</span>
                                            <span class="font-bold" x-text="lng.toFixed(6)"></span>
                                        </div>
                                        <div class="flex justify-between mt-1 border-t border-emerald-100 pt-1">
                                            <span>Akurasi GPS:</span>
                                            <span x-text="'± ' + Math.round(accuracy) + ' meter'"></span>
                                        </div>
                                        <template x-if="distance !== null">
                                            <div class="flex justify-between mt-1 font-sans">
                                                <span>Jarak ke Kantor:</span>
                                                <span class="font-bold" :class="distance > maxRadius ? 'text-red-600' : 'text-emerald-700'" x-text="Math.round(distance) + ' meter'"></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="!checking && errorMsg">
                                    <div class="text-red-700" x-text="errorMsg"></div>
                                </template>
                                <template x-if="!checking && !lat && !errorMsg">
                                    <div class="text-gray-500">Izin lokasi diperlukan. Klik Refresh GPS.</div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 space-y-3">
                    <button type="button" 
                            @click="if(!lat) { alert('GPS koordinat belum didapatkan!'); return; } $wire.clockIn($wire.clock_employee_id, lat, lng)"
                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md font-bold text-sm shadow-sm transition cursor-pointer"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        CLOCK IN (Masuk)
                    </button>
                    <button type="button" 
                            @click="if(!lat) { alert('GPS koordinat belum didapatkan!'); return; } $wire.clockOut($wire.clock_employee_id, lat, lng)"
                            class="w-full inline-flex justify-center items-center px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white rounded-md font-bold text-sm shadow-sm transition cursor-pointer"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h1a3 3 0 013 3v1"></path></svg>
                        CLOCK OUT (Pulang)
                    </button>
                </div>
            </div>

            <!-- Attendance History Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans lg:col-span-2">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <span class="font-bold text-gray-700 text-sm">Log Kehadiran Karyawan</span>
                    <div class="flex items-center space-x-2">
                        <label class="text-xs text-gray-500 font-semibold">Pilih Tanggal:</label>
                        <input type="date" wire:model.live="att_filter_date" class="border border-gray-300 rounded px-2.5 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                        <thead>
                            <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                                <th class="py-3 px-4">Nama Karyawan</th>
                                <th class="py-3 px-4">Clock In</th>
                                <th class="py-3 px-4">Clock Out</th>
                                <th class="py-3 px-4 text-center">Jarak Check In</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-gray-50 transition duration-150">
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-gray-900">{{ $att->employee->name }}</div>
                                        <div class="text-[10px] text-gray-500 font-mono">{{ $att->employee->employee_number }}</div>
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-gray-700">
                                        <span class="font-bold text-gray-900">{{ $att->check_in }}</span>
                                        @if($att->check_in_latitude)
                                            <span class="block text-[9px] text-gray-400 font-normal">GPS: {{ round($att->check_in_latitude, 5) }}, {{ round($att->check_in_longitude, 5) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 font-mono text-gray-700">
                                        <span class="font-bold text-gray-900">{{ $att->check_out ?? '--:--' }}</span>
                                        @if($att->check_out_latitude)
                                            <span class="block text-[9px] text-gray-400 font-normal">GPS: {{ round($att->check_out_latitude, 5) }}, {{ round($att->check_out_longitude, 5) }}</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center font-mono font-medium">
                                        @if($att->check_in_distance !== null)
                                            <span class="px-2 py-0.5 rounded {{ $att->check_in_distance > ($attendanceSetting ? $attendanceSetting->allowed_radius : 200) ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                                {{ round($att->check_in_distance) }} meter
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                                            {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                            {{ $att->status === 'late' ? 'bg-amber-100 text-amber-800' : '' }}
                                            {{ $att->status === 'absent' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $att->status === 'leave' ? 'bg-blue-100 text-blue-800' : '' }}
                                        ">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                    <td class="py-3.5 px-4 text-gray-500 font-medium max-w-[150px] truncate" title="{{ $att->notes }}">
                                        {{ $att->notes ?: '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-gray-500">
                                        Tidak ada data absensi untuk tanggal {{ Carbon\Carbon::parse($att_filter_date)->translatedFormat('d F Y') }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($attendances->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $attendances->links() }}
                    </div>
                @endif
            </div>
        </div>
    @elseif($activeTab === 'payroll')
        <!-- Payroll Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Period</th>
                            <th class="py-3.5 px-6">Employee</th>
                            <th class="py-3.5 px-6 text-right">Basic Salary</th>
                            <th class="py-3.5 px-6 text-right">Allowances</th>
                            <th class="py-3.5 px-6 text-right">Deductions</th>
                            <th class="py-3.5 px-6 text-right">Take Home Pay</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payrolls as $pr)
                            <tr class="hover:bg-gray-50 transition duration-150" x-data="{ showSlip: false }">
                                <td class="py-4 px-6 font-mono text-gray-900">{{ $pr->period }}</td>
                                <td class="py-4 px-6 font-medium text-gray-700">{{ $pr->employee->name }}</td>
                                <td class="py-4 px-6 text-right font-mono">Rp {{ number_format($pr->basic_salary, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-emerald-600">+Rp {{ number_format($pr->allowances, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono text-red-600">-Rp {{ number_format($pr->deductions, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">Rp {{ number_format($pr->total_salary, 0, ',', '.') }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pr->status === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($pr->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    @if($pr->status === 'draft')
                                        <button wire:click="processPayment({{ $pr->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Pay</button>
                                    @else
                                        <span class="text-gray-400">Paid</span>
                                    @endif
                                </td>
                            </tr>
                            <!-- Expandable Slip Gaji Detail Row -->
                            @if($pr->components && $pr->components->count() > 0)
                            <tr class="bg-blue-50 bg-opacity-50">
                                <td colspan="8" class="py-0 px-6">
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open" class="text-xs text-blue-600 font-semibold py-2 cursor-pointer hover:underline">
                                            <span x-text="open ? '▾ Hide Slip Gaji Detail' : '▸ View Slip Gaji Detail'"></span>
                                        </button>
                                        <div x-show="open" x-transition class="pb-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-2xl">
                                                <!-- Allowances -->
                                                <div>
                                                    <h5 class="text-xs font-bold text-emerald-700 uppercase mb-2">Allowances</h5>
                                                    @foreach($pr->components->where('type', 'allowance') as $comp)
                                                        <div class="flex justify-between text-xs text-gray-600 border-b border-gray-100 py-1">
                                                            <span>{{ $comp->name }}</span>
                                                            <span class="font-mono text-emerald-600">+Rp {{ number_format($comp->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <!-- Deductions -->
                                                <div>
                                                    <h5 class="text-xs font-bold text-red-700 uppercase mb-2">Deductions</h5>
                                                    @foreach($pr->components->where('type', 'deduction') as $comp)
                                                        <div class="flex justify-between text-xs text-gray-600 border-b border-gray-100 py-1">
                                                            <span>{{ $comp->name }}</span>
                                                            <span class="font-mono text-red-600">-Rp {{ number_format($comp->amount, 0, ',', '.') }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-gray-500">
                                    No payroll records generated yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($payrolls->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $payrolls->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'departments')
        <!-- Departments List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Department Name</th>
                            <th class="py-3.5 px-6">Description</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($departments as $dept)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $dept->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $dept->description ?: '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dept->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $dept->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    <button wire:click="editDepartment({{ $dept->id }})" class="text-blue-600 hover:text-blue-900 font-medium cursor-pointer">Edit</button>
                                    <button wire:click="deleteDepartment({{ $dept->id }})" wire:confirm="Delete this department?" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-500">
                                    No departments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($departments->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>
    @elseif($activeTab === 'leaves')
        <!-- Leaves request list -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden font-sans">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left font-semibold text-gray-500">
                            <th class="py-3.5 px-6">Employee</th>
                            <th class="py-3.5 px-6">Leave Period</th>
                            <th class="py-3.5 px-6">Type</th>
                            <th class="py-3.5 px-6">Reason</th>
                            <th class="py-3.5 px-6 text-center">Status</th>
                            <th class="py-3.5 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($leaves as $leave)
                            <tr class="hover:bg-gray-50 transition duration-150">
                                <td class="py-4 px-6 font-medium text-gray-900">{{ $leave->employee->name }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ $leave->start_date }} to {{ $leave->end_date }}</td>
                                <td class="py-4 px-6 text-gray-700 font-medium">{{ ucfirst($leave->type) }}</td>
                                <td class="py-4 px-6 text-gray-500">{{ $leave->reason ?: '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $leave->status === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                        {{ $leave->status === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                        {{ $leave->status === 'rejected' ? 'bg-red-100 text-red-800' : '' }}
                                    ">
                                        {{ ucfirst($leave->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center space-x-2">
                                    @if($leave->status === 'pending')
                                        <button wire:click="approveLeave({{ $leave->id }})" class="text-emerald-600 hover:text-emerald-900 font-medium cursor-pointer">Approve</button>
                                        <button wire:click="rejectLeave({{ $leave->id }})" class="text-red-600 hover:text-red-900 font-medium cursor-pointer">Reject</button>
                                    @else
                                        <span class="text-gray-400 font-medium">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    No leave requests logged.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($leaves->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $leaves->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Modals Section -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="closeModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <!-- Modal employee -->
                    @if($modalType === 'employee')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                {{ $emp_id ? 'Edit Employee' : 'Add New Employee' }}
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee ID Code</label>
                                    <input type="text" wire:model="employee_number" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('employee_number') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" wire:model="emp_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('emp_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" wire:model="emp_email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('emp_email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                                        <input type="text" wire:model="emp_phone" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Department</label>
                                        <input type="text" wire:model="emp_department" placeholder="e.g. Sales, Finance, IT" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Position</label>
                                        <input type="text" wire:model="emp_position" placeholder="e.g. Manager, Staff" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Join Date</label>
                                        <input type="date" wire:model="emp_join_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Basic Salary (Rp)</label>
                                        <input type="number" wire:model="emp_salary" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('emp_salary') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="emp_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveEmployee" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Employee
                            </button>
                        </div>

                    <!-- Modal payroll -->
                    @elseif($modalType === 'payroll')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                Generate Payroll Draft
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Period Month</label>
                                    <select wire:model="payroll_month" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        @for($m = 1; $m <= 12; $m++)
                                            <option value="{{ $m }}">{{ date('F', mktime(0, 0, 0, $m, 10)) }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Period Year</label>
                                    <input type="number" wire:model="payroll_year" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="generatePayroll" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Generate Draft
                            </button>
                        </div>

                    <!-- Modal department -->
                    @elseif($modalType === 'department')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                {{ $dept_id ? 'Edit Department' : 'Add New Department' }}
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Department Name</label>
                                    <input type="text" wire:model="dept_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('dept_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea wire:model="dept_description" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" rows="3"></textarea>
                                </div>
                                <div>
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" wire:model="dept_is_active" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <span class="ml-2 text-sm font-medium text-gray-700">Is Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveDepartment" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Save Department
                            </button>
                        </div>

                    <!-- Modal leave -->
                    @elseif($modalType === 'leave')
                        <div>
                            <h3 class="text-lg leading-6 font-medium text-gray-900 font-display" id="modal-title">
                                {{ $leave_id ? 'Edit Leave Request' : 'Request Cuti' }}
                            </h3>
                            <div class="mt-4 space-y-4 font-sans">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Employee</label>
                                    <select wire:model="leave_employee_id" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="">Select Employee</option>
                                        @foreach($allEmployees as $e)
                                            <option value="{{ $e->id }}">{{ $e->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('leave_employee_id') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                        <input type="date" wire:model="leave_start_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('leave_start_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">End Date</label>
                                        <input type="date" wire:model="leave_end_date" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                        @error('leave_end_date') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Leave Type</label>
                                    <select wire:model="leave_type" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="annual">Annual Cuti</option>
                                        <option value="sick">Sick / Sakit</option>
                                        <option value="personal">Personal / Izin Keperluan Pribadi</option>
                                        <option value="maternity">Maternity / Cuti Hamil/Melahirkan</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Reason / Rationale</label>
                                    <input type="text" wire:model="leave_reason" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Cancel
                            </button>
                            <button type="button" wire:click="saveLeave" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold">
                                Submit Leave Request
                            </button>
                        </div>
                    @elseif($modalType === 'attendance-settings')
                        <div>
                            <h3 class="text-lg leading-6 font-semibold text-gray-900 font-display" id="modal-title">
                                Pengaturan Absensi (GPS & Jam Kerja)
                            </h3>
                            <div class="mt-4 space-y-4 font-sans text-sm">
                                <div>
                                    <label class="block font-medium text-gray-700">Nama Kantor / Lokasi</label>
                                    <input type="text" wire:model="setting_office_name" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    @error('setting_office_name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-gray-700">Office Latitude</label>
                                        <input type="number" step="0.0000001" wire:model="setting_latitude" placeholder="-6.200000" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_latitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700">Office Longitude</label>
                                        <input type="number" step="0.0000001" wire:model="setting_longitude" placeholder="106.816666" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_longitude') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-gray-700">Allowed Radius (Meter)</label>
                                        <input type="number" wire:model="setting_radius" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_radius') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex items-center pt-6">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" wire:model="setting_require_location" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                            <span class="ml-2 font-medium text-gray-700">Wajib Lokasi GPS</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-gray-700">Jam Mulai Kerja</label>
                                        <input type="time" wire:model="setting_work_start" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_work_start') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700">Jam Selesai Kerja</label>
                                        <input type="time" wire:model="setting_work_end" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_work_end') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block font-medium text-gray-700">Toleransi Terlambat (Menit)</label>
                                        <input type="number" wire:model="setting_late_tolerance" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_late_tolerance') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block font-medium text-gray-700">Batas Cepat Clock-In (Menit)</label>
                                        <input type="number" wire:model="setting_early_checkin" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @error('setting_early_checkin') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" wire:click="closeModal" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none font-display">
                                Batal
                            </button>
                            <button type="button" wire:click="saveAttendanceSettings" class="inline-flex justify-center px-4 py-2 text-sm font-semibold text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display">
                                Simpan Pengaturan
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
