<!-- Creative Vibrant Theme (creative) -->
<div class="bg-indigo-50 text-slate-900 antialiased selection:bg-orange-500 selection:text-white min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 glass border-b border-indigo-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                <span class="text-2xl font-black font-display text-indigo-900 tracking-tight">WIKASA<span class="text-orange-500">MEBEL</span></span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-bold text-slate-700">
                <a href="#about" class="hover:text-orange-500 transition">About</a>
                <a href="#services" class="hover:text-orange-500 transition">Services</a>
                <a href="#values" class="hover:text-orange-500 transition">Values</a>
                <a href="#gallery" class="hover:text-orange-500 transition">Gallery</a>
                <a href="#contact" class="hover:text-orange-500 transition">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section (Full Width Background Image, Centered Text) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-indigo-950 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-indigo-950/75 backdrop-blur-[2px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-orange-500 text-white uppercase tracking-widest">BOLD DESIGN</span>
                <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight text-white font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-lg md:text-xl text-indigo-200 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $banner->short_description }}
                </p>
                <div class="flex items-center justify-center gap-4 pt-6">
                    <a href="#services" class="px-8 py-4 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-full shadow-lg transition transform hover:-translate-y-0.5">Explore agency services</a>
                </div>
            </div>
        </section>
    @empty
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-indigo-950 text-white" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-indigo-950/80"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <h1 class="text-5xl md:text-7xl font-black leading-tight tracking-tight text-white font-display">
                    Bold Business Solutions
                </h1>
            </div>
        </section>
    @endforelse

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-24 bg-indigo-50 relative overflow-hidden border-b border-indigo-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-orange-500">ABOUT US</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-indigo-950 font-display leading-tight">
                            {{ $about->title }}
                        </h2>
                        <div class="text-slate-700 leading-relaxed space-y-4 font-light text-base font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                    </div>
                    <div class="lg:col-span-6 grid grid-cols-2 gap-4">
                        @if($about->image_1)
                            <img src="{{ asset('storage/' . $about->image_1) }}" class="rounded-[2rem] shadow-md w-full aspect-[4/5] object-cover border-4 border-white">
                        @endif
                        @if($about->image_2)
                            <img src="{{ asset('storage/' . $about->image_2) }}" class="rounded-[2rem] shadow-md w-full aspect-[4/5] object-cover border-4 border-white pt-8">
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-orange-500 bg-orange-100 px-3 py-1 rounded-full">CREATIVE SERVICES</span>
                <h2 class="text-4xl md:text-5xl font-black text-indigo-950 font-display">
                    {{ $service_parent->title ?? 'Services We Provide' }}
                </h2>
                <p class="text-lg text-slate-600 font-light leading-relaxed">
                    {{ $service_parent->description ?? '' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($services as $service)
                    <div class="group bg-indigo-50 rounded-[2rem] p-8 border-2 border-transparent hover:border-orange-500 hover:bg-white transition duration-300 flex flex-col justify-between shadow-sm hover:shadow-xl">
                        <div class="space-y-6">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover rounded-2xl shadow-sm mb-4">
                            @endif
                            <h3 class="text-2xl font-bold text-indigo-950 font-display">
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
