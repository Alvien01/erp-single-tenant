<!-- Dynamic Tech Theme (tech) -->
<div class="bg-slate-950 text-slate-100 antialiased selection:bg-cyan-500 selection:text-slate-950 min-h-screen relative overflow-hidden">

    <!-- Glowing Tech Grids and Circles in Background -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#0f172a_1px,transparent_1px),linear-gradient(to_bottom,#0f172a_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)]"></div>
    <div class="absolute top-0 left-1/4 w-[500px] h-[500px] bg-cyan-500/10 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute top-1/3 right-1/4 w-[600px] h-[600px] bg-purple-500/10 rounded-full blur-[150px] pointer-events-none"></div>

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass-dark border-b border-slate-900">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                <span class="text-2xl font-black font-display bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent tracking-tight">WIKASA<span class="text-slate-100">MEBEL</span></span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-400">
                <a href="#about" class="hover:text-cyan-400 transition">Infrastructure</a>
                <a href="#services" class="hover:text-cyan-400 transition">Services</a>
                <a href="#values" class="hover:text-cyan-400 transition">Protocol</a>
                <a href="#gallery" class="hover:text-cyan-400 transition">Nodes</a>
                <a href="#contact" class="hover:text-cyan-400 transition">Terminal</a>
            </nav>

            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-gradient-to-r from-cyan-500 to-purple-500 hover:from-cyan-600 hover:to-purple-600 text-slate-950 font-bold rounded-lg text-sm transition transform hover:-translate-y-0.5">Initialize</a>
                    @else
                        <a href="{{ route('login') }}" class="px-5 py-2.5 text-slate-300 hover:text-cyan-400 text-sm font-semibold transition">Terminal Login</a>
                    @endauth
                @endif
            </div>
        </div>
    </header>

    <!-- Hero Section (Full Width Tech Image Background, Glowing Center Text) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?q=80&w=2072' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-slate-950/85 backdrop-blur-[2px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 tracking-widest uppercase">QUANTUM CORE ACTIVE</span>
                <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight text-white font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-lg md:text-xl text-slate-400 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $banner->short_description }}
                </p>
                <div class="flex items-center justify-center gap-4 pt-6">
                    <a href="#services" class="px-8 py-3.5 bg-cyan-400 hover:bg-cyan-500 text-slate-950 font-bold rounded-lg shadow-xl shadow-cyan-500/20 transition transform hover:-translate-y-0.5">Access Services</a>
                </div>
            </div>
        </section>
    @empty
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 text-white">
            <div class="absolute inset-0 bg-slate-950/90"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight text-white font-display bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">
                    Quantum Data Engine
                </h1>
            </div>
        </section>
    @endforelse

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-24 relative overflow-hidden border-b border-slate-900">
            <div class="max-w-7xl mx-auto px-6 relative z-10">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-cyan-400">COMPANY PROFILE</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-white font-display leading-tight">
                            {{ $about->title }}
                        </h2>
                        <div class="text-slate-400 leading-relaxed space-y-4 font-light text-base font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                    </div>
                    <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                        @if($about->image_1)
                            <div class="relative group">
                                <div class="absolute -inset-1 rounded-xl bg-cyan-500/20 blur opacity-40 group-hover:opacity-100 transition"></div>
                                <img src="{{ asset('storage/' . $about->image_1) }}" class="relative rounded-xl shadow-md w-full aspect-[4/5] object-cover border border-slate-800">
                            </div>
                        @endif
                        @if($about->image_2)
                            <div class="relative group pt-8">
                                <div class="absolute -inset-1 rounded-xl bg-purple-500/20 blur opacity-40 group-hover:opacity-100 transition"></div>
                                <img src="{{ asset('storage/' . $about->image_2) }}" class="relative rounded-xl shadow-md w-full aspect-[4/5] object-cover border border-slate-800">
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-24 border-b border-slate-900">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-cyan-400">SERVICES DEPLOYED</span>
                <h2 class="text-4xl md:text-5xl font-black text-white font-display">
                    {{ $service_parent->title ?? 'Services' }}
                </h2>
                <p class="text-slate-400 font-light leading-relaxed">
                    {{ $service_parent->description ?? 'Bespoke microservice endpoints for modular control.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="relative group bg-slate-950/80 p-8 rounded-2xl border border-slate-900 hover:border-cyan-500/50 hover:shadow-2xl hover:shadow-cyan-500/5 transition duration-500 flex flex-col justify-between overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-cyan-500/5 to-purple-500/5 opacity-0 group-hover:opacity-100 transition duration-500"></div>
                        <div class="space-y-6 relative z-10">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover rounded-xl shadow-sm border border-slate-800 mb-4">
                            @endif
                            <h3 class="text-2xl font-bold text-white font-display group-hover:text-cyan-400 transition duration-300">
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

</div>
