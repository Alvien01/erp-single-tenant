<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel ERP') }} - @yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=IBM+Plex+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[var(--color-background)] text-[var(--color-text)] min-h-screen w-full max-w-full overflow-x-hidden selection:bg-blue-600 selection:text-white">
    <div class="min-h-screen flex w-full max-w-full overflow-x-hidden" 
         x-data="{ 
             sidebarOpen: window.innerWidth >= 1024,
             init() {
                 window.addEventListener('resize', () => {
                     if (window.innerWidth >= 1024) {
                         this.sidebarOpen = true;
                         document.body.classList.remove('overflow-hidden');
                     }
                 });
                 this.$watch('sidebarOpen', val => {
                     if (window.innerWidth < 1024) {
                         if (val) {
                             document.body.classList.add('overflow-hidden');
                         } else {
                             document.body.classList.remove('overflow-hidden');
                         }
                     }
                 });
             }
         }">
        
        <!-- Sidebar Component -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen min-w-0 w-full max-w-full overflow-x-hidden transition-all duration-300"
             :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
            
            <!-- Topbar Component -->
            <x-topbar />

            <!-- Page Content -->
            <main class="flex-1 p-3.5 sm:p-5 lg:p-6 space-y-4 sm:space-y-6 min-w-0 w-full max-w-full overflow-x-hidden">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
