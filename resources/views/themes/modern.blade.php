<!-- Modern Dark Glassmorphism Theme (modern) -->
<div class="bg-slate-950 text-slate-100 antialiased selection:bg-emerald-500 selection:text-slate-950 min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-dark border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                @if($contact && $contact->logo && $contact->logo !== 'default-logo.png')
                    <img src="{{ asset('storage/' . $contact->logo) }}" class="h-10 object-contain">
                @else
                    <span class="text-2xl font-extrabold font-display text-emerald-500 tracking-tight transition">WIKASA<span class="text-slate-100">MEBEL</span></span>
                @endif
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-400">
                <a href="#about" class="hover:text-emerald-400 transition">About</a>
                <a href="#services" class="hover:text-emerald-400 transition">Services</a>
                <a href="#values" class="hover:text-emerald-400 transition">Values</a>
                <a href="#gallery" class="hover:text-emerald-400 transition">Gallery</a>
                <a href="#testimonials" class="hover:text-emerald-400 transition">Testimonials</a>
                <a href="#contact" class="hover:text-emerald-400 transition">Contact</a>
            </nav>

            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 rounded-lg text-sm font-semibold shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-0.5">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-slate-300 hover:text-emerald-400 text-sm font-semibold transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-100 rounded-lg text-sm font-semibold border border-slate-700 transition">Register</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section (Full Width Background Image, Centered Text) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-slate-950/80 backdrop-blur-[2px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 tracking-widest uppercase">NEXT-GEN TECHNOLOGY</span>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $banner->short_description }}
                </p>
                <div class="flex items-center justify-center gap-4 pt-6">
                    <a href="#services" class="px-8 py-3.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-lg shadow-xl shadow-emerald-500/20 transition transform hover:-translate-y-0.5">Explore Infrastructure</a>
                </div>
            </div>
        </section>
    @empty
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 text-white" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-slate-950/85"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    Robust Enterprise Cloud Management
                </h1>
            </div>
        </section>
    @endforelse

    <!-- Clients logo list -->
    @if($clients->isNotEmpty())
        <section class="py-12 bg-slate-900 border-y border-slate-800">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex flex-wrap items-center justify-center gap-12 md:gap-16 opacity-50">
                    @foreach($clients as $cli)
                        <img src="{{ asset('storage/' . $cli->image) }}" class="h-8 md:h-10 object-contain invert" alt="{{ $cli->title }}">
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-24 bg-slate-950 relative overflow-hidden border-b border-slate-900">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">ABOUT US</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-white font-display leading-tight">
                            {{ $about->title }}
                        </h2>
                        <div class="text-slate-400 leading-relaxed space-y-4 font-light text-base font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                    </div>
                    <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                        @if($about->image_1)
                            <img src="{{ asset('storage/' . $about->image_1) }}" class="rounded-xl shadow-md w-full aspect-[4/5] object-cover border border-slate-800">
                        @endif
                        @if($about->image_2)
                            <img src="{{ asset('storage/' . $about->image_2) }}" class="rounded-xl shadow-md w-full aspect-[4/5] object-cover border border-slate-800 pt-8">
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-24 bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-emerald-400">CORE ARCHITECTURE</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-white font-display">
                    {{ $service_parent->title ?? 'Services' }}
                </h2>
                <p class="text-slate-400 font-light leading-relaxed">
                    {{ $service_parent->description ?? '' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="bg-slate-950 rounded-xl p-8 border border-slate-800 hover:border-emerald-500/50 transition duration-300 flex flex-col justify-between">
                        <div class="space-y-6">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover rounded-lg shadow-sm border border-slate-800 mb-4">
                            @endif
                            <h3 class="text-2xl font-bold text-white font-display">
                                {{ $service->title }}
                            </h3>
                            <p class="text-slate-400 font-light text-sm leading-relaxed line-clamp-3 font-sans">
                                {{ $service->short_description }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact & Footer -->
    <section id="contact" class="py-24 bg-slate-950 text-white border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <div class="lg:col-span-5 space-y-8">
                    <span class="text-3xl font-extrabold font-display text-emerald-500">Modern<span class="text-slate-100">ERP</span></span>
                    <div class="space-y-4 text-slate-400 text-sm">
                        <p>📍 {{ $contact->alamat_kantor ?? 'Not Configured' }}</p>
                        <p>✉️ {{ $contact->email ?? 'Not Configured' }}</p>
                    </div>
                </div>
                <div class="lg:col-span-7 relative h-[300px] rounded-xl overflow-hidden border border-slate-800">
                    @if($contact && $contact->iframe)
                        {!! $contact->iframe !!}
                    @endif
                </div>
            </div>
        </div>
    </section>

</div>
