<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel ERP') }} - POS Terminal</title>

    <!-- PWA / Mobile Web App Setup -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0f172a">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        /* Hide scrollbar for clean POS look */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: #475569; }

        /* Scrollbar utility classes */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a; /* Slate 900 */
            color: #f8fafc; /* Slate 50 */
            overscroll-behavior-y: contain; /* Prevent pull-to-refresh on mobile */
            -webkit-text-size-adjust: 100%;
        }
        
        .font-display { font-family: 'Outfit', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        
        .glass-panel {
            background: rgba(30, 41, 59, 0.7); /* Slate 800 */
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        /* Safe area support for notch screens */
        header {
            padding-top: env(safe-area-inset-top, 0px);
            padding-left: env(safe-area-inset-left, 1rem);
            padding-right: env(safe-area-inset-right, 1rem);
        }
    </style>
</head>
<body class="antialiased overflow-hidden h-screen w-screen flex flex-col">
    <!-- Top Navigation Bar for POS -->
    <header class="glass-panel border-b border-slate-700/50 min-h-14 py-2 sm:py-0 shrink-0 px-4 flex items-center justify-between z-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('dashboard') }}" class="text-slate-400 hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="font-display font-bold text-xl tracking-tight text-white flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span>POS<span class="text-blue-400">Terminal</span></span>
            </div>
        </div>
        
        <div class="flex items-center gap-4 text-sm font-medium">
            <div class="hidden md:flex items-center gap-2 text-slate-300 bg-slate-800/50 px-3 py-1.5 rounded-full border border-slate-700/50">
                <div class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></div>
                <span id="clock" class="font-mono">{{ now()->format('d M Y - H:i') }}</span>
            </div>
            
            <div class="flex items-center gap-2 px-3 py-1.5 bg-slate-800 rounded-lg border border-slate-700">
                <div class="w-6 h-6 rounded-full bg-slate-600 flex items-center justify-center text-xs font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <span class="text-slate-200 truncate max-w-[100px]">{{ Auth::user()->name }}</span>
            </div>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 flex overflow-hidden relative">
        {{ $slot }}
    </main>

    @livewireScripts
    
    <script>
        // Simple clock updater
        setInterval(() => {
            const clock = document.getElementById('clock');
            if(clock) {
                const now = new Date();
                const opts = { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' };
                clock.textContent = now.toLocaleDateString('en-GB', opts).replace(',', ' -');
            }
        }, 60000);
        
        // Listen for scanner input globally (if not typing in an input)
        let barcodeBuffer = '';
        let barcodeTimeout;
        
        document.addEventListener('keydown', (e) => {
            // Ignore if typing in an input field
            if (['INPUT', 'TEXTAREA', 'SELECT'].includes(e.target.tagName)) return;
            
            // If Enter is pressed, submit barcode to Livewire
            if (e.key === 'Enter' && barcodeBuffer.length > 2) {
                Livewire.dispatch('barcodeScanned', { barcode: barcodeBuffer });
                barcodeBuffer = '';
                return;
            }
            
            // Append character
            if (e.key.length === 1) {
                barcodeBuffer += e.key;
                
                // Clear buffer if too slow (likely typing, not scanning)
                clearTimeout(barcodeTimeout);
                barcodeTimeout = setTimeout(() => {
                    barcodeBuffer = '';
                }, 500); // 500ms max between keystrokes for barcode
            }
        });
    </script>
</body>
</html>
