<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900">Content Management</h1>
            <p class="text-sm text-gray-500 mt-1">Manage all child lists, banners, about sections, taglines, and testimonies on your homepage.</p>
        </div>
        @if(in_array($activeTab, ['banner', 'service', 'value', 'gallery', 'client', 'testimoni']))
            <button wire:click="openModal" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Create {{ ucfirst($activeTab) }}
            </button>
        @endif
    </div>

    <!-- Success/Error Alerts -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display overflow-x-auto whitespace-nowrap">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            @foreach(['banner' => 'Banners', 'about' => 'About Us', 'service' => 'Our Services', 'value' => 'Our Values', 'gallery' => 'Gallery', 'client' => 'Our Clients', 'tagline' => 'Tagline', 'testimoni' => 'Testimonials', 'contact' => 'Contact Us', 'template' => 'Themes Selection'] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Active Tab Panel -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 font-sans">
        @if($activeTab === 'banner')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Banner List</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($banners as $banner)
                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow transition">
                        @if($banner->image)
                            <img src="{{ asset('storage/' . $banner->image) }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-lg text-gray-900">{{ $banner->title }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $banner->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($banner->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $banner->short_description }}</p>
                            <div class="flex justify-end space-x-2 pt-2 border-t text-sm">
                                <button wire:click="editBanner({{ $banner->id }})" class="text-blue-600 hover:text-blue-900 font-semibold cursor-pointer">Edit</button>
                                <button wire:click="deleteBanner({{ $banner->id }})" wire:confirm="Delete this banner?" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No Banners added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'about')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Edit About Us Section</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">About Title</label>
                    <input type="text" wire:model="about_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('about_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea wire:model="about_description" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                    @error('about_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image 1</label>
                        @if($about_image_1)
                            <div class="mb-2"><img src="{{ asset('storage/' . $about_image_1) }}" class="h-20 w-32 object-cover rounded"></div>
                        @endif
                        <input type="file" wire:model="uploadImage1" class="mt-1 block w-full text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Image 2</label>
                        @if($about_image_2)
                            <div class="mb-2"><img src="{{ asset('storage/' . $about_image_2) }}" class="h-20 w-32 object-cover rounded"></div>
                        @endif
                        <input type="file" wire:model="uploadImage2" class="mt-1 block w-full text-sm">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Video Link / URL</label>
                    <input type="text" wire:model="about_video" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. YouTube or direct link">
                </div>
                <div class="flex justify-end pt-4">
                    <button wire:click="saveAboutUs" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold cursor-pointer">
                        Save About Us
                    </button>
                </div>
            </div>

        @elseif($activeTab === 'service')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Services Child List</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($services as $service)
                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow transition">
                        @if($service->image)
                            <img src="{{ asset('storage/' . $service->image) }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-lg text-gray-900">{{ $service->title }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $service->short_description }}</p>
                            <div class="flex justify-end space-x-2 pt-2 border-t text-sm">
                                <button wire:click="editService({{ $service->id }})" class="text-blue-600 hover:text-blue-900 font-semibold cursor-pointer">Edit</button>
                                <button wire:click="deleteService({{ $service->id }})" wire:confirm="Delete this service?" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No Services added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'value')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Our Values Child List</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($values as $val)
                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow transition">
                        @if($val->image)
                            <img src="{{ asset('storage/' . $val->image) }}" class="h-48 w-full object-cover">
                        @endif
                        <div class="p-4 space-y-2">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-lg text-gray-900">{{ $val->title }}</h4>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $val->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($val->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $val->short_description }}</p>
                            <div class="flex justify-end space-x-2 pt-2 border-t text-sm">
                                <button wire:click="editValue({{ $val->id }})" class="text-blue-600 hover:text-blue-900 font-semibold cursor-pointer">Edit</button>
                                <button wire:click="deleteValue({{ $val->id }})" wire:confirm="Delete this value?" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No Values added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'gallery')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Gallery Child List</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @forelse($galleries as $gal)
                    <div class="border rounded-lg overflow-hidden relative group">
                        <img src="{{ asset('storage/' . $gal->image) }}" class="h-40 w-full object-cover">
                        <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition flex flex-col justify-end p-3 text-white">
                            <p class="font-semibold text-sm">{{ $gal->title }}</p>
                            <div class="flex justify-end space-x-2 mt-2 text-xs">
                                <button wire:click="editGallery({{ $gal->id }})" class="text-blue-300 hover:text-white cursor-pointer">Edit</button>
                                <button wire:click="deleteGallery({{ $gal->id }})" wire:confirm="Delete this image?" class="text-red-300 hover:text-white cursor-pointer">Delete</button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No gallery items added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'client')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Clients Child List</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($clients as $cli)
                    <div class="border rounded-lg p-4 flex flex-col items-center justify-between text-center relative group">
                        <img src="{{ asset('storage/' . $cli->image) }}" class="h-16 object-contain mb-3">
                        <h4 class="font-semibold text-sm text-gray-900">{{ $cli->title }}</h4>
                        <span class="mt-1 px-2 py-0.5 text-xs rounded-full {{ $cli->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($cli->status) }}
                        </span>
                        <div class="absolute top-2 right-2 space-x-1 opacity-0 group-hover:opacity-100 transition text-xs">
                            <button wire:click="editClient({{ $cli->id }})" class="text-blue-600 font-bold hover:underline cursor-pointer">Edit</button>
                            <button wire:click="deleteClient({{ $cli->id }})" class="text-red-600 font-bold hover:underline cursor-pointer">Del</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No Clients added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'tagline')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Edit Tagline Settings</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tagline Title</label>
                    <input type="text" wire:model="tagline_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('tagline_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Keterangan Tagline</label>
                    <input type="text" wire:model="tagline_keterangan" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('tagline_keterangan') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">WhatsApp Tagline (Number)</label>
                    <input type="text" wire:model="tagline_wa" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. 628123456789">
                    @error('tagline_wa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div class="flex justify-end pt-4">
                    <button wire:click="saveTagline" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold cursor-pointer">
                        Save Tagline Settings
                    </button>
                </div>
            </div>

        @elseif($activeTab === 'testimoni')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Testimonials Child List</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($testimonis as $test)
                    <div class="border rounded-lg p-5 shadow-sm space-y-3 relative">
                        <div class="flex items-center space-x-3">
                            @if($test->image)
                                <img src="{{ asset('storage/' . $test->image) }}" class="h-12 w-12 rounded-full object-cover">
                            @endif
                            <div>
                                <h4 class="font-bold text-gray-900">{{ $test->nama_customer }}</h4>
                                <p class="text-xs text-gray-500">{{ $test->title }}</p>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 italic">"{{ $test->short_description }}"</p>
                        <span class="inline-block px-2.5 py-0.5 text-xs rounded-full {{ $test->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($test->status) }}
                        </span>
                        <div class="flex justify-end space-x-2 pt-2 border-t text-sm">
                            <button wire:click="editTestimoni({{ $test->id }})" class="text-blue-600 hover:text-blue-900 font-semibold cursor-pointer">Edit</button>
                            <button wire:click="deleteTestimoni({{ $test->id }})" wire:confirm="Delete testimonial?" class="text-red-600 hover:text-red-900 font-semibold cursor-pointer">Delete</button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">No testimonials added yet.</div>
                @endforelse
            </div>

        @elseif($activeTab === 'contact')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Edit Contact Us Settings</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Office Address</label>
                    <input type="text" wire:model="contact_alamat" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('contact_alamat') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" wire:model="contact_email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('contact_email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fax</label>
                    <input type="text" wire:model="contact_fax" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('contact_fax') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Opening Hours</label>
                    <input type="text" wire:model="contact_jam_buka" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('contact_jam_buka') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Google Map Iframe Link</label>
                    <input type="text" wire:model="contact_iframe" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">WhatsApp Phone (no_wa)</label>
                    <input type="text" wire:model="contact_no_wa" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">WhatsApp Starter Text</label>
                    <input type="text" wire:model="contact_text_wa" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                    @error('contact_text_wa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Facebook URL</label>
                    <input type="text" wire:model="contact_facebook" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Twitter URL</label>
                    <input type="text" wire:model="contact_twitter" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">TikTok URL</label>
                    <input type="text" wire:model="contact_tiktok" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div class="col-span-full">
                    <label class="block text-sm font-medium text-gray-700">Logo</label>
                    @if($contact_logo)
                        <div class="mb-2"><img src="{{ asset('storage/' . $contact_logo) }}" class="h-16 object-contain"></div>
                    @endif
                    <input type="file" wire:model="uploadLogo" class="mt-1 block w-full text-sm">
                </div>
            </div>
            <div class="flex justify-end pt-4">
                <button wire:click="saveContactUs" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-semibold cursor-pointer">
                    Save Contact Settings
                </button>
            </div>
        @elseif($activeTab === 'template')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-2 font-bold">Select Homepage Theme Template</h3>
            <p class="text-sm text-gray-500 mb-6">Choose a pre-built layout theme for your homepage. All of your contents, services, contact details, and testimonials will dynamically configure to match the selected layout aesthetic instantly!</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Theme 1: Default -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'default' ? 'border-blue-600 ring-2 ring-blue-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="default" class="sr-only">
                    <div class="h-32 bg-slate-100 flex items-center justify-center p-4">
                        <span class="text-3xl font-black text-blue-600">Laravel<span class="text-slate-900">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Classic Blue Theme</span>
                            @if($active_template === 'default')
                                <span class="w-2.5 h-2.5 bg-blue-600 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Light minimalist design with clean Outfit typography and corporate blue accents.</p>
                    </div>
                </label>

                <!-- Theme 2: Modern Dark -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'modern' ? 'border-emerald-500 ring-2 ring-emerald-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="modern" class="sr-only">
                    <div class="h-32 bg-slate-950 flex items-center justify-center p-4 border-b border-slate-800">
                        <span class="text-3xl font-black text-emerald-400">Modern<span class="text-white">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Next-Gen Dark Theme</span>
                            @if($active_template === 'modern')
                                <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">High-tech dark background with stunning glassmorphism details and emerald highlights.</p>
                    </div>
                </label>

                <!-- Theme 3: Creative -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'creative' ? 'border-orange-500 ring-2 ring-orange-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="creative" class="sr-only">
                    <div class="h-32 bg-indigo-950 flex items-center justify-center p-4">
                        <span class="text-3xl font-black text-white">Creative<span class="text-orange-500">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Vibrant Creative Theme</span>
                            @if($active_template === 'creative')
                                <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Playful, asymmetric fluid design with deep indigo contrasts and bright orange CTA buttons.</p>
                    </div>
                </label>

                <!-- Theme 4: Minimalist Elegant -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'minimalist' ? 'border-stone-900 ring-2 ring-stone-900/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="minimalist" class="sr-only">
                    <div class="h-32 bg-stone-100 flex items-center justify-center p-4">
                        <span class="text-2xl font-light text-stone-900 tracking-widest uppercase">MINIMALIST<span class="font-bold text-stone-500">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Minimalist Luxury Theme</span>
                            @if($active_template === 'minimalist')
                                <span class="w-2.5 h-2.5 bg-stone-900 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Stunning high-luxury layout with elegant thin typography, monochrome tones, and spacious layouts.</p>
                    </div>
                </label>

                <!-- Theme 5: Dynamic Cyber Tech -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'tech' ? 'border-cyan-500 ring-2 ring-cyan-500/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="tech" class="sr-only">
                    <div class="h-32 bg-slate-950 flex items-center justify-center p-4">
                        <span class="text-3xl font-black bg-gradient-to-r from-cyan-400 to-purple-400 bg-clip-text text-transparent">Cyber<span class="text-slate-100">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Quantum Cyber Theme</span>
                            @if($active_template === 'tech')
                                <span class="w-2.5 h-2.5 bg-cyan-500 rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">High-end sci-fi style with dark digital matrix backgrounds, cyber glowing borders and purple gradients.</p>
                    </div>
                </label>

                <!-- Theme 6: Majestic Corporate -->
                <label class="relative block border-2 rounded-2xl overflow-hidden cursor-pointer group transition duration-300 {{ $active_template === 'corporate' ? 'border-[#116466] ring-2 ring-[#116466]/20' : 'border-gray-200 hover:border-gray-300' }}">
                    <input type="radio" wire:model="active_template" value="corporate" class="sr-only">
                    <div class="h-32 bg-[#2C3531] flex items-center justify-center p-4">
                        <span class="text-2xl font-extrabold text-[#D1A054] tracking-tight">Majestic<span class="text-white">ERP</span></span>
                    </div>
                    <div class="p-4 border-t">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-gray-900 font-display">Majestic Sage Theme</span>
                            @if($active_template === 'corporate')
                                <span class="w-2.5 h-2.5 bg-[#116466] rounded-full"></span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Sleek, authoritative high-end corporate style featuring prestigious sage green and sand tones.</p>
                    </div>
                </label>
            </div>

            <div class="flex justify-end pt-8 mt-8 border-t">
                <button wire:click="saveTemplate" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold shadow-md shadow-blue-500/10 cursor-pointer">
                    Activate Homepage Theme
                </button>
            </div>
        @endif
    </div>

    <!-- Create/Edit Modals for all entities -->
    @if($isOpen)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" wire:click="$set('isOpen', false)"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button type="button" wire:click="$set('isOpen', false)" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900 font-display mb-4" id="modal-title">
                            {{ $isEditMode ? 'Edit ' . ucfirst($activeTab) : 'Create ' . ucfirst($activeTab) }}
                        </h3>

                        <div class="space-y-4">
                            @if($activeTab === 'banner')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Banner Title</label>
                                    <input type="text" wire:model="banner_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('banner_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Short Description</label>
                                    <input type="text" wire:model="banner_short_description" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('banner_short_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="banner_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                            @elseif($activeTab === 'service')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Service Title</label>
                                    <input type="text" wire:model="service_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('service_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Short Description</label>
                                    <textarea wire:model="service_short_description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                                    @error('service_short_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="service_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                            @elseif($activeTab === 'value')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Value Title</label>
                                    <input type="text" wire:model="value_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('value_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Short Description</label>
                                    <textarea wire:model="value_short_description" rows="2" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                                    @error('value_short_description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="value_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                            @elseif($activeTab === 'gallery')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title</label>
                                    <input type="text" wire:model="gallery_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('gallery_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                            @elseif($activeTab === 'client')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Client Name</label>
                                    <input type="text" wire:model="client_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('client_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Link (Optional)</label>
                                    <input type="text" wire:model="client_link" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Image Logo</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="client_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>

                            @elseif($activeTab === 'testimoni')
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Title / Occupation</label>
                                    <input type="text" wire:model="testimoni_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm" placeholder="e.g. CEO, Founder">
                                    @error('testimoni_title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                                    <input type="text" wire:model="testimoni_customer" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                                    @error('testimoni_customer') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Short Testimonial Description</label>
                                    <textarea wire:model="testimoni_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                                    @error('testimoni_desc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Customer Avatar</label>
                                    <input type="file" wire:model="uploadImage" class="mt-1 block w-full text-sm">
                                    @error('uploadImage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select wire:model="testimoni_status" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm bg-white">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" wire:click="$set('isOpen', false)" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none">
                            Cancel
                        </button>
                        <button type="button" wire:click="save{{ ucfirst($activeTab) }}" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none">
                            Save details
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
