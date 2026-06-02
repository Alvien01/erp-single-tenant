<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel ERP') }} - Login</title>

        <!-- Fonts (matching admin layout) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=IBM+Plex+Sans:wght@400;500;600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }

            body.guest-body {
                font-family: 'Inter', 'DM Sans', sans-serif;
                min-height: 100vh;
                background: linear-gradient(135deg, #f0f4ff 0%, #e8edf5 25%, #f5f7fa 50%, #eef1f8 75%, #f0f4ff 100%);
                background-size: 400% 400%;
                animation: gradientShift 15s ease infinite;
            }

            @keyframes gradientShift {
                0%, 100% { background-position: 0% 50%; }
                50% { background-position: 100% 50%; }
            }

            .login-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(20px);
                -webkit-backdrop-filter: blur(20px);
                border: 1px solid rgba(229, 231, 235, 0.8);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 
                             0 10px 15px -3px rgba(0, 0, 0, 0.05),
                             0 20px 25px -5px rgba(0, 0, 0, 0.03);
                transition: box-shadow 0.3s ease, transform 0.3s ease;
            }

            .login-card:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 
                             0 10px 15px -3px rgba(0, 0, 0, 0.07),
                             0 25px 50px -12px rgba(0, 0, 0, 0.08);
            }

            .brand-text {
                font-family: 'Plus Jakarta Sans', 'DM Sans', sans-serif;
            }

            /* Floating shapes background decoration */
            .bg-decoration {
                position: fixed;
                border-radius: 50%;
                opacity: 0.06;
                pointer-events: none;
            }

            .bg-decoration-1 {
                width: 400px;
                height: 400px;
                background: #2563eb;
                top: -100px;
                right: -100px;
                animation: float1 20s ease-in-out infinite;
            }

            .bg-decoration-2 {
                width: 300px;
                height: 300px;
                background: #3b82f6;
                bottom: -80px;
                left: -80px;
                animation: float2 25s ease-in-out infinite;
            }

            .bg-decoration-3 {
                width: 200px;
                height: 200px;
                background: #1d4ed8;
                top: 50%;
                left: 60%;
                animation: float3 18s ease-in-out infinite;
            }

            @keyframes float1 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(-40px, 40px) scale(1.1); }
            }
            @keyframes float2 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(30px, -30px) scale(1.05); }
            }
            @keyframes float3 {
                0%, 100% { transform: translate(0, 0) scale(1); }
                50% { transform: translate(-20px, -20px) scale(0.95); }
            }
        </style>
    </head>
    <body class="guest-body antialiased">
        <!-- Background decorations -->
        <div class="bg-decoration bg-decoration-1"></div>
        <div class="bg-decoration bg-decoration-2"></div>
        <div class="bg-decoration bg-decoration-3"></div>

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">
            <!-- Brand Header -->
            <div class="mb-8 text-center">
                <a href="/" wire:navigate class="inline-flex items-center space-x-3 group">
                    <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-600/20 group-hover:shadow-blue-600/30 transition-shadow duration-300">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <span class="brand-text text-2xl font-bold">
                        <span class="text-blue-600">Wikasa</span><span class="text-gray-900">Mebel</span>
                    </span>
                </a>
                <p class="mt-3 text-sm text-gray-500 font-medium">Enterprise Resource Planning System</p>
            </div>

            <!-- Login Card -->
            <div class="w-full sm:max-w-md px-8 py-8 login-card rounded-2xl">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="mt-8 text-center">
                <p class="text-xs text-gray-400 font-medium">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel ERP') }}. All rights reserved.
                </p>
            </div>
        </div>
    </body>
</html>
