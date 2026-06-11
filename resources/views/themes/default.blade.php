<!-- Premium Classic Theme (Default) -->
<div class="bg-slate-50 text-slate-800 antialiased selection:bg-blue-600 selection:text-white">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 right-0 z-50 transition-all duration-300 glass border-b border-white/20">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="#" class="flex items-center space-x-3 group">
                @if($contact && $contact->logo && $contact->logo !== 'default-logo.png')
                    <img src="{{ asset('storage/' . $contact->logo) }}" class="h-10 object-contain">
                @else
                    <span class="text-2xl font-extrabold font-display text-blue-600 tracking-tight group-hover:text-blue-700 transition">WIKASA<span class="text-slate-900">MEBEL</span></span>
                @endif
            </a>
            
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-600">
                <a href="#about" class="hover:text-blue-600 transition">About</a>
                <a href="#services" class="hover:text-blue-600 transition">Services</a>
                <a href="#values" class="hover:text-blue-600 transition">Values</a>
                <a href="#gallery" class="hover:text-blue-600 transition">Gallery</a>
                <a href="#testimonials" class="hover:text-blue-600 transition">Testimonials</a>
                <a href="#contact" class="hover:text-blue-600 transition">Contact</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section (Banners - Full Width Background Image, Centered Text) -->
    @forelse($banners as $index => $banner)
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-950 text-white {{ $index > 0 ? 'hidden' : '' }}" style="background-image: url('{{ $banner->image ? asset('storage/' . $banner->image) : 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070' }}'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[2px]"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30 tracking-widest uppercase">LATEST UPDATES</span>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    {{ $banner->title }}
                </h1>
                <p class="text-lg md:text-xl text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $banner->short_description }}
                </p>
                <div class="flex items-center justify-center gap-4 pt-6">
                    <a href="#services" class="px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-xl shadow-blue-500/20 transition transform hover:-translate-y-0.5">Our Services</a>
                    <a href="#contact" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-semibold rounded-xl border border-white/20 transition">Contact Us</a>
                </div>
            </div>
        </section>
    @empty
        <!-- Fallback Banner -->
        <section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-slate-900 text-white" style="background-image: url('https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?q=80&w=2070'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-slate-950/70"></div>
            <div class="relative max-w-4xl mx-auto px-6 py-24 z-10 w-full text-center space-y-6">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-500/20 text-blue-300 border border-blue-400/30">WELCOME TO ENTERPRISE</span>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-tight tracking-tight text-white font-display">
                    Empower Your Business Workflow
                </h1>
                <p class="text-lg text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
                    Integrate accounting, HR, warehouse, sales and procurement under one premium platform.
                </p>
            </div>
        </section>
    @endforelse

    <!-- Clients logo list -->
    @if($clients->isNotEmpty())
        <section class="py-12 bg-white border-y border-slate-200">
            <div class="max-w-7xl mx-auto px-6">
                <p class="text-center text-xs font-bold uppercase tracking-wider text-slate-400 mb-8">
                    {{ $client_parent->title ?? 'TRUSTED BY GLOBAL ENTERPRISES' }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-12 md:gap-16">
                    @foreach($clients as $cli)
                        <a href="{{ $cli->link ?? '#' }}" target="_blank" class="grayscale hover:grayscale-0 opacity-60 hover:opacity-100 transition duration-300">
                            <img src="{{ asset('storage/' . $cli->image) }}" class="h-8 md:h-10 object-contain" alt="{{ $cli->title }}">
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About Us Section -->
    @if($about)
        <section id="about" class="py-24 bg-slate-50 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                    <div class="lg:col-span-6 space-y-6">
                        <span class="text-xs font-bold uppercase tracking-widest text-blue-600">ABOUT US</span>
                        <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 font-display leading-tight">
                            {{ $about->title }}
                        </h2>
                        <div class="text-slate-600 leading-relaxed space-y-4 font-light text-base font-sans">
                            {!! nl2br(e($about->description)) !!}
                        </div>
                        @if($about->video)
                            <div class="pt-4">
                                <a href="{{ $about->video }}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-bold transition">
                                    <span class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg></span>
                                    Watch Company Profile Video
                                </a>
                            </div>
                        @endif
                    </div>
                    <div class="lg:col-span-6 grid grid-cols-2 gap-4 relative">
                        <div class="space-y-4">
                            @if($about->image_1)
                                <img src="{{ asset('storage/' . $about->image_1) }}" class="rounded-2xl shadow-md w-full aspect-[4/5] object-cover border border-white">
                            @endif
                        </div>
                        <div class="space-y-4 pt-8">
                            @if($about->image_2)
                                <img src="{{ asset('storage/' . $about->image_2) }}" class="rounded-2xl shadow-md w-full aspect-[4/5] object-cover border border-white">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- Our Services Section -->
    <section id="services" class="py-24 bg-white border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600">OUR SERVICES</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 font-display">
                    {{ $service_parent->title ?? 'Services We Provide' }}
                </h2>
                <p class="text-lg text-slate-600 font-light leading-relaxed">
                    {{ $service_parent->description ?? 'Premium integrations designed to grow business.' }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($services as $service)
                    <div class="group bg-slate-50 rounded-2xl p-8 border border-slate-100 shadow-sm hover:shadow-xl hover:border-slate-200 transition duration-300 flex flex-col justify-between">
                        <div class="space-y-6">
                            @if($service->image)
                                <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover rounded-xl shadow-sm mb-4">
                            @endif
                            <h3 class="text-2xl font-bold text-slate-900 font-display group-hover:text-blue-600 transition">
                                {{ $service->title }}
                            </h3>
                            <p class="text-slate-600 font-light text-sm leading-relaxed line-clamp-3">
                                {{ $service->short_description }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">No services have been configured yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Our Values Section -->
    <section id="values" class="py-24 bg-slate-50 relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
                <div class="lg:col-span-5 space-y-6">
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-600">OUR CORE VALUES</span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 font-display leading-tight">
                        {{ $value_parent->title ?? 'Driven by Excellence' }}
                    </h2>
                    <p class="text-lg text-slate-600 font-light leading-relaxed">
                        {{ $value_parent->description ?? 'We run on strict parameters of compliance.' }}
                    </p>
                    @if($value_parent && $value_parent->image)
                        <img src="{{ asset('storage/' . $value_parent->image) }}" class="rounded-2xl shadow-lg w-full aspect-video object-cover border border-slate-200">
                    @endif
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @forelse($values as $val)
                            <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm hover:shadow transition">
                                @if($val->image)
                                    <img src="{{ asset('storage/' . $val->image) }}" class="h-16 w-16 object-cover rounded-xl mb-4">
                                @endif
                                <h4 class="text-lg font-bold text-slate-900 font-display mb-2">{{ $val->title }}</h4>
                                <p class="text-slate-600 text-xs font-light leading-relaxed line-clamp-3">{{ $val->short_description }}</p>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center text-slate-400">No values configured.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Tagline Section -->
    @if($tagline)
        <section class="py-20 bg-slate-900 text-white relative overflow-hidden">
            <div class="max-w-5xl mx-auto px-6 text-center space-y-8 relative z-10">
                <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight font-display text-white">
                    {{ $tagline->title_tagline }}
                </h2>
                <p class="text-lg text-slate-300 max-w-2xl mx-auto font-light leading-relaxed">
                    {{ $tagline->keterangan_tagline }}
                </p>
                <div class="pt-4">
                    <a href="https://wa.me/{{ $tagline->wa_tagline }}" target="_blank" class="inline-flex items-center px-8 py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-full shadow-xl transition">
                        Chat via WhatsApp
                    </a>
                </div>
            </div>
        </section>
    @endif

    <!-- Product Gallery Section -->
    <section id="gallery" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                <span class="text-xs font-bold uppercase tracking-widest text-blue-600">GALLERY</span>
                <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 font-display">
                    {{ $gallery_parent->title ?? 'Explore Our Gallery' }}
                </h2>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse($galleries as $gal)
                    <div class="group border rounded-2xl overflow-hidden shadow-sm relative aspect-square transition duration-300 hover:shadow-lg">
                        <img src="{{ asset('storage/' . $gal->image) }}" class="h-full w-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute inset-0 bg-slate-950/60 opacity-0 group-hover:opacity-100 transition flex flex-col justify-end p-6 text-white">
                            <h4 class="font-bold text-lg font-display">{{ $gal->title }}</h4>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-slate-400">No images configured.</div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    @if($testimonis->isNotEmpty())
        <section id="testimonials" class="py-24 bg-slate-50 border-t border-slate-100">
            <div class="max-w-7xl mx-auto px-6">
                <div class="text-center max-w-3xl mx-auto mb-20 space-y-4">
                    <span class="text-xs font-bold uppercase tracking-widest text-blue-600">TESTIMONIALS</span>
                    <h2 class="text-4xl md:text-5xl font-extrabold text-slate-900 font-display">
                        {{ $testimoni_parent->title ?? 'What Clients Say' }}
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($testimonis as $test)
                        <div class="bg-white rounded-2xl p-8 border border-slate-100 shadow-sm flex flex-col justify-between">
                            <p class="text-slate-600 italic font-light text-sm leading-relaxed">
                                "{{ $test->short_description }}"
                            </p>
                            <div class="flex items-center space-x-4 pt-6 border-t border-slate-100 mt-6">
                                @if($test->image)
                                    <img src="{{ asset('storage/' . $test->image) }}" class="h-12 w-12 rounded-full object-cover">
                                @endif
                                <div>
                                    <h4 class="font-bold text-slate-900 text-sm font-display">{{ $test->nama_customer }}</h4>
                                    <p class="text-xs text-slate-500">{{ $test->title }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Contact & Footer Section -->
    <section id="contact" class="py-24 bg-slate-900 text-white relative">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <div class="lg:col-span-5 space-y-8">
                    <span class="text-3xl font-extrabold font-display text-blue-500">Laravel<span class="text-white">ERP</span></span>
                    <p class="text-slate-400 font-light leading-relaxed max-w-md">
                        Transform your enterprise infrastructure with our premier ERP integration modules.
                    </p>
                    <div class="space-y-4 text-slate-300 text-sm">
                        <p>📍 {{ $contact->alamat_kantor ?? 'Not Configured' }}</p>
                        <p>✉️ {{ $contact->email ?? 'Not Configured' }}</p>
                        <p>🕒 {{ $contact->jam_buka ?? 'Not Configured' }}</p>
                    </div>
                </div>

                <div class="lg:col-span-7 relative h-[400px] rounded-2xl overflow-hidden border border-white/10">
                    @if($contact && $contact->iframe)
                        {!! $contact->iframe !!}
                    @endif
                </div>
            </div>
        </div>
    </section>

</div>
