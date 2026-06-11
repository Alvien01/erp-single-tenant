<!-- Majestic Corporate Theme (corporate) -->
<div class="bg-[#FBFBFA] text-[#2C3531] antialiased selection:bg-[#116466] selection:text-white min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass border-b border-[#E8E7E3]">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                <span class="text-2xl font-extrabold font-display text-[#116466] tracking-tight">WIKASA<span class="text-[#D1A054]">MEBEL</span></span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold uppercase tracking-wider text-[#2C3531]/80">
                <a href="#about" class="hover:text-[#116466] transition">About Us</a>
                <a href="#services" class="hover:text-[#116466] transition">Services</a>
                <a href="#values" class="hover:text-[#116466] transition">Values</a>
                <a href="#gallery" class="hover:text-[#116466] transition">Portfolio</a>
                <a href="#contact" class="hover:text-[#116466] transition">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section (Full Width Majestic Corporate Image, Centered Typography) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-[#2C3531]/80 backdrop-blur-[2px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-4 py-1.5 rounded bg-[#116466]/20 text-[#D1A054] border border-[#D1A054]/30 text-xs font-bold tracking-widest uppercase">LEADERS IN INTEGRATION</span>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-lg md:text-xl text-[#E8E7E3] max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $banner->short_description }}
                </p>
                <div class="flex items-center justify-center gap-4 pt-6">
                    <a href="#services" class="px-8 py-3 bg-[#116466] hover:bg-[#0F5A5C] text-white font-bold rounded shadow-lg transition">Company Solutions</a>
                </div>
            </div>
        </section>
    @empty
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900 text-white" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-[#2C3531]/85"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    Majestic Corporate Workflow Solutions
                </h1>
            </div>
        </section>
    @endforelse

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-24 bg-[#E8E7E3]/30 border-b border-[#E8E7E3]">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-[#116466]">OUR HERITAGE</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-[#2C3531] font-display leading-tight">
                            {{ $about->title }}
                        </h2>
                        <div class="text-[#2C3531]/80 leading-relaxed space-y-4 font-light text-base font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                    </div>
                    <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                        @if($about->image_1)
                            <img src="{{ asset('storage/' . $about->image_1) }}" class="rounded shadow-md w-full aspect-[4/5] object-cover border border-[#E8E7E3]">
                        @endif
                        @if($about->image_2)
                            <img src="{{ asset('storage/' . $about->image_2) }}" class="rounded shadow-md w-full aspect-[4/5] object-cover border border-[#E8E7E3] pt-8">
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-24 bg-[#FBFBFA] border-b border-[#E8E7E3]">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-[#116466]">ENTERPRISE ARCHITECTURE</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-[#2C3531] font-display">
                    {{ $service_parent->title ?? 'Services' }}
                </h2>
                <p class="text-slate-600 font-light leading-relaxed">
                    {{ $service_parent->description ?? '' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="bg-white rounded p-8 border border-[#E8E7E3] hover:border-[#116466] hover:shadow-xl transition duration-300 flex flex-col justify-between">
                        <div class="space-y-6">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover rounded shadow-sm mb-4 border border-[#E8E7E3]">
                            @endif
                            <h3 class="text-2xl font-bold text-[#2C3531] font-display">
                                {{ $service->title }}
                            </h3>
                            <p class="text-slate-600 font-light text-sm leading-relaxed line-clamp-3 font-sans">
                                {{ $service->short_description }}
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
