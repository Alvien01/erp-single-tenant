<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel ERP') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=IBM+Plex+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-[var(--color-background)] text-[var(--color-text)]">
    <div class="min-h-screen flex" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        
        <!-- Sidebar Component -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-300"
             :class="sidebarOpen ? 'lg:ml-64' : 'lg:ml-0'">
            
            <!-- Topbar Component -->
            <x-topbar />

            <!-- Page Content -->
            <main class="flex-1 p-6 space-y-4">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
