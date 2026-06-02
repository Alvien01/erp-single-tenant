<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold font-display text-gray-900">Basic Configuration (Parent Content)</h1>
            <p class="text-sm text-gray-500 mt-1">Configure section titles, headers, descriptions, and structural texts displayed on the home page sections.</p>
        </div>
    </div>

    <!-- Success Alerts -->
    @if (session()->has('success'))
        <div class="p-4 mb-4 text-sm text-emerald-800 rounded-lg bg-emerald-50 border border-emerald-200" role="alert">
            <span class="font-medium">Success!</span> {{ session('success') }}
        </div>
    @endif

    <!-- Navigation Tabs -->
    <div class="border-b border-gray-200 font-display overflow-x-auto whitespace-nowrap">
        <nav class="-mb-px flex space-x-6" aria-label="Tabs">
            @foreach([
                'banner' => 'Config Banner',
                'about' => 'Config About Us',
                'service' => 'Config Our Services',
                'gallery' => 'Config Our Gallery',
                'news' => 'Config News',
                'value' => 'Config Value',
                'testimoni' => 'Config Testimoni',
                'tagline' => 'Config Tagline',
                'contact' => 'Config Contact Us'
            ] as $tab => $label)
                <button wire:click="setTab('{{ $tab }}')" class="py-4 px-1 border-b-2 font-medium text-sm transition-colors cursor-pointer {{ $activeTab === $tab ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </nav>
    </div>

    <!-- Active Tab Configuration Panel -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 font-sans">
        
        <!-- Config Banner -->
        @if($activeTab === 'banner')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Banner Title & Subtitle</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Main Banner Title (Header)</label>
                    <input type="text" wire:model="banner_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Banner Paragraph / Subtitle (<p>)</label>
                    <textarea wire:model="banner_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config About Us -->
        @elseif($activeTab === 'about')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config About Us Title & Narrative</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">About Us Main Heading (Title)</label>
                    <input type="text" wire:model="about_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">About Us Full Paragraph / Narrative (<p>)</label>
                    <textarea wire:model="about_p_desc" rows="5" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config Our Services -->
        @elseif($activeTab === 'service')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Services Section Header</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Services Section Title</label>
                    <input type="text" wire:model="service_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Services Section Paragraph (<p>)</label>
                    <textarea wire:model="service_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config Our Gallery -->
        @elseif($activeTab === 'gallery')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Gallery Section Header</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gallery Section Title</label>
                    <input type="text" wire:model="gallery_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Gallery Section Paragraph (<p>)</label>
                    <textarea wire:model="gallery_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config News -->
        @elseif($activeTab === 'news')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config News Section Header</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">News Section Title</label>
                    <input type="text" wire:model="news_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">News Section Paragraph (<p>)</label>
                    <textarea wire:model="news_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config Value -->
        @elseif($activeTab === 'value')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Core Value Section Header</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Core Values Section Title</label>
                    <input type="text" wire:model="value_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Core Values Section Paragraph (<p>)</label>
                    <textarea wire:model="value_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Section Banner / Showcase Image</label>
                    @if($value_p_image)
                        <div class="mb-2"><img src="{{ asset('storage/' . $value_p_image) }}" class="h-32 object-cover rounded shadow-sm"></div>
                    @endif
                    <input type="file" wire:model="uploadValuePImage" class="mt-1 block w-full text-sm">
                </div>
            </div>

        <!-- Config Testimoni -->
        @elseif($activeTab === 'testimoni')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Testimonials Section Header</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Testimonials Section Title</label>
                    <input type="text" wire:model="testimoni_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Testimonials Section Paragraph (<p>)</label>
                    <textarea wire:model="testimoni_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
            </div>

        <!-- Config Tagline -->
        @elseif($activeTab === 'tagline')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Tagline Header & Phone</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tagline Highlight Heading</label>
                    <input type="text" wire:model="tagline_p_title" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tagline Highlight Description / Sub-keterangan (<p>)</label>
                    <textarea wire:model="tagline_p_desc" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tagline WhatsApp Phone Number</label>
                    <input type="text" wire:model="tagline_p_wa" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
            </div>

        <!-- Config Contact Us -->
        @elseif($activeTab === 'contact')
            <h3 class="text-lg font-medium text-gray-900 font-display mb-4">Config Contact Us Core Profiles</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Main Office Address</label>
                    <input type="text" wire:model="contact_p_alamat" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Official Email</label>
                    <input type="email" wire:model="contact_p_email" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Fax Number</label>
                    <input type="text" wire:model="contact_p_fax" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Office Working Hours</label>
                    <input type="text" wire:model="contact_p_jam" class="mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 sm:text-sm">
                </div>
                <div class="col-span-full">
                    <label class="block text-sm font-medium text-gray-700">Official Brand Logo</label>
                    @if($contact_p_logo)
                        <div class="mb-2"><img src="{{ asset('storage/' . $contact_p_logo) }}" class="h-16 object-contain"></div>
                    @endif
                    <input type="file" wire:model="uploadLogo" class="mt-1 block w-full text-sm">
                </div>
            </div>
        @endif

        <div class="flex justify-end pt-6 mt-6 border-t">
            <button type="button" wire:click="saveConfig" class="inline-flex justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none font-display font-semibold cursor-pointer">
                Save Section Configuration
            </button>
        </div>
    </div>
</div>
