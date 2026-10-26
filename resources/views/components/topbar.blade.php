@php
    $searchableMenus = \App\Models\MenuSetting::getSearchableMenus();
@endphp

<header class="bg-white border-b border-gray-200 sticky top-0 z-30"
        x-data="erpMenuSearch(@js($searchableMenus))"
        @keydown.window.prevent.ctrl.k="focusSearch()"
        @keydown.window.prevent.cmd.k="focusSearch()">
    <div class="flex items-center justify-between px-4 sm:px-6 py-3">
        
        <!-- Left Side: Mobile Menu Button & Search -->
        <div class="flex items-center flex-1 mr-4">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-blue-600 focus:outline-none mr-3 lg:mr-4 transition duration-150">
                <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <!-- Mobile Search Trigger Button -->
            <button @click="openMobileModal()" type="button" class="md:hidden p-2 text-gray-500 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors focus:outline-none" title="Search menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>

            <!-- Desktop Search Bar -->
            <div class="relative w-full max-w-lg hidden md:block" @click.outside="open = false">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-gray-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </span>
                    <input 
                        x-ref="desktopSearchInput"
                        x-model="query"
                        @focus="open = true; selectedIndex = 0"
                        @input="open = true; selectedIndex = 0"
                        @keydown.arrow-down.prevent="selectNext()"
                        @keydown.arrow-up.prevent="selectPrev()"
                        @keydown.enter.prevent="selectCurrent()"
                        @keydown.escape.prevent="open = false; $refs.desktopSearchInput.blur()"
                        type="text" 
                        class="w-full pl-10 pr-20 py-2 bg-gray-50 hover:bg-white focus:bg-white border border-gray-300 rounded-lg text-sm text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all shadow-xs" 
                        placeholder="Search menu or feature... (e.g. Sales, Kas, Retur)"
                        autocomplete="off"
                        spellcheck="false">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-2.5">
                        <template x-if="query.length > 0">
                            <button @click="clear(); $refs.desktopSearchInput.focus()" type="button" class="p-1 text-gray-400 hover:text-gray-600 rounded-md hover:bg-gray-100 transition-colors" title="Clear search">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </template>
                        <template x-if="query.length === 0">
                            <div class="hidden lg:flex items-center gap-0.5 text-[11px] font-mono text-gray-400 bg-white border border-gray-200 px-1.5 py-0.5 rounded shadow-2xs pointer-events-none">
                                <span class="text-[10px]">Ctrl</span>
                                <span>K</span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Dropdown Results (Desktop) -->
                <div x-show="open" 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-[0.98]"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-[0.98]"
                     class="absolute left-0 right-0 mt-2 bg-white rounded-xl shadow-2xl border border-gray-200 py-2 z-50 overflow-hidden font-sans"
                     style="display: none;">

                    <div class="px-3 pb-1.5 mb-1 border-b border-gray-100 flex items-center justify-between text-[11px] font-semibold uppercase tracking-wider text-gray-400">
                        <span x-text="query.trim() ? 'Search Results (' + filteredMenus.length + ')' : 'Quick Menu Navigation'"></span>
                        <span class="text-[10px] font-normal text-gray-400 lowercase">Press <kbd class="px-1 py-0.2 bg-gray-100 border border-gray-200 rounded font-mono text-[9px]">↵</kbd> to visit</span>
                    </div>

                    <ul x-ref="resultsList" class="max-h-80 overflow-y-auto divide-y divide-gray-50 focus:outline-none">
                        <template x-for="(item, index) in filteredMenus" :key="item.url">
                            <li>
                                <a :href="item.url" 
                                   @mouseenter="selectedIndex = index"
                                   :class="{
                                       'bg-blue-50 text-blue-900 border-l-4 border-blue-600': selectedIndex === index,
                                       'text-gray-700 hover:bg-gray-50 border-l-4 border-transparent': selectedIndex !== index
                                   }"
                                   class="flex items-center justify-between px-3.5 py-2.5 transition-colors group cursor-pointer">
                                    <div class="flex items-center min-w-0 gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 text-xs font-bold transition-colors"
                                             :class="{
                                                 'bg-blue-600 text-white shadow-xs': selectedIndex === index,
                                                 'bg-gray-100 text-gray-600 group-hover:bg-blue-100 group-hover:text-blue-700': selectedIndex !== index
                                             }">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-medium text-sm text-gray-900 group-hover:text-blue-600 truncate" x-text="item.name"></div>
                                            <div class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-0.5">
                                                <span class="inline-block px-1.5 py-0.2 rounded border text-[10px] font-medium"
                                                      :class="getGroupBadgeClass(item.group)"
                                                      x-text="item.group"></span>
                                                <span class="text-gray-300">•</span>
                                                <span class="truncate font-mono text-[10px] text-gray-400" x-text="'/' + item.route"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shrink-0 flex items-center ml-2">
                                        <span class="text-xs text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1 font-semibold"
                                              :class="{'opacity-100': selectedIndex === index}">
                                            Open
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                        </span>
                                    </div>
                                </a>
                            </li>
                        </template>
                        <template x-if="filteredMenus.length === 0">
                            <li class="px-4 py-8 text-center">
                                <svg class="w-10 h-10 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <p class="text-sm font-medium text-gray-700">No menu found</p>
                                <p class="text-xs text-gray-400 mt-1">No ERP menu or feature matches "<span class="font-semibold text-gray-600" x-text="query"></span>"</p>
                            </li>
                        </template>
                    </ul>
                    
                    <div class="px-3 pt-2 pb-1 border-t border-gray-100 bg-gray-50/70 flex items-center justify-between text-[11px] text-gray-400">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center gap-1"><kbd class="px-1 bg-white border border-gray-200 rounded text-[9px]">↑</kbd><kbd class="px-1 bg-white border border-gray-200 rounded text-[9px]">↓</kbd> navigate</span>
                            <span class="flex items-center gap-1"><kbd class="px-1 bg-white border border-gray-200 rounded text-[9px]">↵</kbd> select</span>
                            <span class="flex items-center gap-1"><kbd class="px-1 bg-white border border-gray-200 rounded text-[9px]">esc</kbd> close</span>
                        </div>
                        <span class="text-[10px]">ERP Menu Navigator</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Notifications & Profile -->
        <div class="flex items-center space-x-3 sm:space-x-4">
            
            <!-- Notifications -->
            <button class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none focus:text-gray-500">
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
            </button>

            <!-- Language Switcher -->
            <div x-data="{ langOpen: false }" class="relative">
                <button @click="langOpen = !langOpen" @click.away="langOpen = false" class="flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 hover:border-gray-300 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <span id="lang-toggle-flag" class="text-base leading-none">🇬🇧</span>
                    <span id="lang-toggle-label" class="hidden sm:inline font-semibold text-xs tracking-wide">EN</span>
                    <svg class="w-3.5 h-3.5 text-gray-400" :class="{'rotate-180': langOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="langOpen" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1.5 z-50" style="display: none;">
                    <button @click="ERPTranslator.setLang('en'); langOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-blue-50 transition-colors group">
                        <span class="text-lg">🇬🇧</span>
                        <div class="text-left">
                            <span class="font-semibold text-gray-800 group-hover:text-blue-700">English</span>
                            <p class="text-[10px] text-gray-400">English (Default)</p>
                        </div>
                    </button>
                    <button @click="ERPTranslator.setLang('id'); langOpen = false" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm hover:bg-blue-50 transition-colors group">
                        <span class="text-lg">🇮🇩</span>
                        <div class="text-left">
                            <span class="font-semibold text-gray-800 group-hover:text-blue-700">Indonesia</span>
                            <p class="text-[10px] text-gray-400">Bahasa Indonesia</p>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Profile Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" class="flex items-center focus:outline-none">
                    <div class="w-8 h-8 rounded-full bg-blue-100 border border-blue-200 text-blue-700 font-bold flex items-center justify-center text-xs shadow-xs">
                        {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <span class="ml-2 text-sm font-medium text-gray-700 hidden md:block">{{ auth()->user()->name ?? 'Administrator' }}</span>
                    <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 border border-gray-200" style="display: none;">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@erp.local' }}</p>
                    </div>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile Settings</a>
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Sign out</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Mobile Search Modal Dialog -->
    <div x-show="mobileModal" 
         x-transition.opacity
         class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs p-4 flex flex-col md:hidden" 
         style="display: none;"
         @click.self="mobileModal = false">
        <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[85vh] mt-2" @click.outside="mobileModal = false">
            <div class="p-3 border-b border-gray-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-gray-400 shrink-0 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input 
                    x-ref="mobileSearchInput"
                    x-model="query"
                    @keydown.arrow-down.prevent="selectNext()"
                    @keydown.arrow-up.prevent="selectPrev()"
                    @keydown.enter.prevent="selectCurrent()"
                    @keydown.escape.prevent="mobileModal = false"
                    type="text" 
                    class="w-full py-1 text-base text-gray-900 placeholder-gray-400 focus:outline-none" 
                    placeholder="Search menu or feature..."
                    autocomplete="off">
                <button @click="mobileModal = false" type="button" class="p-1.5 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <ul class="overflow-y-auto divide-y divide-gray-100 p-2 max-h-[70vh]">
                <template x-for="(item, index) in filteredMenus" :key="item.url">
                    <li>
                        <a :href="item.url" 
                           class="flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 active:bg-blue-100 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                </div>
                                <div>
                                    <div class="font-semibold text-sm text-gray-900" x-text="item.name"></div>
                                    <div class="text-xs text-gray-500 flex items-center gap-1.5 mt-0.5">
                                        <span class="px-1.5 py-0.2 rounded border text-[10px]" :class="getGroupBadgeClass(item.group)" x-text="item.group"></span>
                                        <span class="text-gray-300">•</span>
                                        <span class="font-mono text-[10px]" x-text="'/' + item.route"></span>
                                    </div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </li>
                </template>
                <template x-if="filteredMenus.length === 0">
                    <li class="py-8 text-center text-gray-500 text-sm">
                        No menu found for "<span class="font-semibold text-gray-700" x-text="query"></span>"
                    </li>
                </template>
            </ul>
        </div>
    </div>
</header>

<script>
function erpMenuSearch(menusData) {
    return {
        query: '',
        open: false,
        mobileModal: false,
        selectedIndex: 0,
        menus: menusData || [],
        focusSearch() {
            if (window.innerWidth < 768) {
                this.openMobileModal();
            } else {
                this.$refs.desktopSearchInput?.focus();
                this.open = true;
                this.selectedIndex = 0;
            }
        },
        openMobileModal() {
            this.mobileModal = true;
            this.query = '';
            this.selectedIndex = 0;
            this.$nextTick(() => {
                this.$refs.mobileSearchInput?.focus();
            });
        },
        get filteredMenus() {
            if (!this.query.trim()) {
                return this.menus.slice(0, 8);
            }
            const q = this.query.toLowerCase().trim();
            const words = q.split(/\s+/).filter(Boolean);
            return this.menus.filter(item => {
                const target = (item.name + ' ' + item.group + ' ' + (item.keywords || '')).toLowerCase();
                return words.every(word => target.includes(word));
            }).slice(0, 10);
        },
        selectNext() {
            if (this.filteredMenus.length > 0) {
                this.selectedIndex = (this.selectedIndex + 1) % this.filteredMenus.length;
                this.scrollToActive();
            }
        },
        selectPrev() {
            if (this.filteredMenus.length > 0) {
                this.selectedIndex = (this.selectedIndex - 1 + this.filteredMenus.length) % this.filteredMenus.length;
                this.scrollToActive();
            }
        },
        selectCurrent() {
            if (this.filteredMenus.length > 0 && this.filteredMenus[this.selectedIndex]) {
                window.location.href = this.filteredMenus[this.selectedIndex].url;
            }
        },
        scrollToActive() {
            this.$nextTick(() => {
                const activeEl = this.$refs.resultsList?.children[this.selectedIndex];
                if (activeEl) {
                    activeEl.scrollIntoView({ block: 'nearest' });
                }
            });
        },
        clear() {
            this.query = '';
            this.selectedIndex = 0;
            this.open = false;
        },
        getGroupBadgeClass(group) {
            switch (group) {
                case 'Master Data': return 'bg-blue-50 text-blue-700 border-blue-200';
                case 'Sales Workflow': return 'bg-emerald-50 text-emerald-700 border-emerald-200';
                case 'Manufacturing': return 'bg-purple-50 text-purple-700 border-purple-200';
                case 'Warehouse & Stock': return 'bg-amber-50 text-amber-700 border-amber-200';
                case 'HR & Recruitment': return 'bg-pink-50 text-pink-700 border-pink-200';
                case 'Accounting & SIA': return 'bg-cyan-50 text-cyan-700 border-cyan-200';
                case 'Procurement': return 'bg-indigo-50 text-indigo-700 border-indigo-200';
                case 'Operations': return 'bg-orange-50 text-orange-700 border-orange-200';
                case 'Content':
                case 'Config Basic': return 'bg-teal-50 text-teal-700 border-teal-200';
                case 'Website & Marketing': return 'bg-rose-50 text-rose-700 border-rose-200';
                default: return 'bg-gray-100 text-gray-700 border-gray-200';
            }
        }
    };
}
</script>
