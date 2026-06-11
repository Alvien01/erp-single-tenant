<!-- Minimalist Elegant Theme (minimalist) -->
<div class="bg-stone-50 text-stone-900 antialiased selection:bg-stone-900 selection:text-white min-h-screen">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 glass border-b border-stone-100">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                <span class="text-xl font-light font-display text-stone-900 tracking-widest uppercase">WIKASA<span class="font-bold text-stone-500">MEBEL</span></span>
            </a>
            
            <nav class="hidden md:flex items-center space-x-10 text-xs font-semibold uppercase tracking-widest text-stone-500">
                <a href="#about" class="hover:text-stone-900 transition">About</a>
                <a href="#services" class="hover:text-stone-900 transition">Services</a>
                <a href="#values" class="hover:text-stone-900 transition">Values</a>
                <a href="#gallery" class="hover:text-stone-900 transition">Gallery</a>
                <a href="#contact" class="hover:text-stone-900 transition">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section (Full Width Background, Minimalist Overlay) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-stone-950 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-stone-950/80 backdrop-blur-[1px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-8">
                <span class="inline-flex items-center text-xs tracking-widest uppercase text-stone-400 font-semibold font-display">LATEST UPDATES</span>
                <h1 class="text-5xl md:text-7xl font-extralight leading-tight tracking-wide text-stone-100 font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-md md:text-lg text-stone-400 max-w-xl mx-auto font-light leading-relaxed tracking-wide font-sans">
                    {{ $banner->short_description }}
                </p>
                <div class="pt-6">
                    <a href="#services" class="inline-block px-10 py-3 bg-stone-100 hover:bg-stone-200 text-stone-950 text-xs uppercase tracking-widest font-bold transition">Explore Services</a>
                </div>
            </div>
        </section>
    @empty
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-stone-950 text-white" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-stone-950/85"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <h1 class="text-5xl md:text-7xl font-extralight leading-tight tracking-tight text-white font-display uppercase tracking-widest">
                    Pure Workspace Integrity
                </h1>
            </div>
        </section>
    @endforelse

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-32 bg-stone-50 border-b border-stone-200/50">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-20 items-start">
                    <div class="lg:col-span-5 space-y-6">
                        <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">ABOUT US</span>
                        <h2 class="text-4xl md:text-5xl font-extralight text-stone-950 font-display tracking-tight leading-tight">
                            {{ $about->title }}
                        </h2>
                    </div>
                    <div class="lg:col-span-7 space-y-8">
                        <div class="text-stone-600 leading-relaxed font-light text-md font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                        <div class="grid grid-cols-2 gap-8 pt-8">
                            @if($about->image_1)
                                <img src="{{ asset('storage/' . $about->image_1) }}" class="grayscale hover:grayscale-0 transition duration-500 rounded-lg w-full aspect-[4/3] object-cover">
                            @endif
                            @if($about->image_2)
                                <img src="{{ asset('storage/' . $about->image_2) }}" class="grayscale hover:grayscale-0 transition duration-500 rounded-lg w-full aspect-[4/3] object-cover">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="max-w-3xl mb-24 space-y-4">
                <span class="text-xs font-semibold uppercase tracking-widest text-stone-400">OUR SERVICES</span>
                <h2 class="text-4xl md:text-5xl font-extralight text-stone-950 font-display tracking-tight">
                    {{ $service_parent->title ?? 'Services' }}
                </h2>
                <p class="text-md text-stone-500 font-light leading-relaxed max-w-2xl font-sans">
                    {{ $service_parent->description ?? 'Bespoke parameters engineered for global workflow stability.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                @foreach($services as $service)
                    <div class="space-y-6 border-b border-stone-100 pb-8 hover:border-stone-900 transition duration-300">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" class="h-60 w-full object-cover rounded-lg grayscale hover:grayscale-0 transition duration-500">
                        @endif
                        <h3 class="text-xl font-medium text-stone-950 font-display">
                            {{ $service->title }}
                        </h3>
                        <p class="text-stone-500 font-light text-sm leading-relaxed font-sans line-clamp-3">
                            {{ $service->short_description }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</div>
