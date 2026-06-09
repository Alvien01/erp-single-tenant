<header class="bg-white border-b border-gray-200 sticky top-0 z-30">
    <div class="flex items-center justify-between px-6 py-3">
        
        <!-- Left Side: Mobile Menu Button & Search -->
        <div class="flex items-center flex-1">
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-blue-600 focus:outline-none mr-4 transition duration-150">
                <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                <svg x-show="sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="relative w-full max-w-md hidden md:block">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" placeholder="Search across ERP...">
            </div>
        </div>

        <!-- Right Side: Notifications & Profile -->
        <div class="flex items-center space-x-4">
            

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
                    <img class="w-8 h-8 rounded-full object-cover border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name ?? 'Admin') }}&color=1D4ED8&background=DBEAFE" alt="User avatar">
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
</header>
