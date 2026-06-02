<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\HomeBanner;
use App\Models\HomeAboutUs;
use App\Models\HomeService;
use App\Models\HomeServiceParent;
use App\Models\HomeValue;
use App\Models\HomeValueParent;
use App\Models\HomeGallery;
use App\Models\HomeGalleryParent;
use App\Models\HomeClient;
use App\Models\HomeClientParent;
use App\Models\HomeTagline;
use App\Models\HomeTestimoni;
use App\Models\HomeTestimoniParent;
use App\Models\HomeContactUs;
use Illuminate\Support\Facades\Storage;

class ContentManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'banner';
    public $isOpen = false;
    public $isEditMode = false;
    public $editId = null;

    // Temporary upload files
    public $uploadImage, $uploadImage1, $uploadImage2, $uploadLogo, $uploadVideo;

    // Form inputs for Banner
    public $banner_title, $banner_short_description, $banner_status = 'active';

    // Form inputs for About Us
    public $about_title, $about_description, $about_image_1, $about_image_2, $about_video;

    // Form inputs for Services Child
    public $service_title, $service_short_description, $service_status = 'active', $service_parent_id;

    // Form inputs for Value Child
    public $value_title, $value_short_description, $value_status = 'active', $value_parent_id;

    // Form inputs for Gallery Child
    public $gallery_title, $gallery_parent_id;

    // Form inputs for Client Child
    public $client_title, $client_link, $client_status = 'active', $client_parent_id;

    // Form inputs for Tagline
    public $tagline_title, $tagline_keterangan, $tagline_wa;

    // Form inputs for Testimoni Child
    public $testimoni_title, $testimoni_customer, $testimoni_desc, $testimoni_status = 'active', $testimoni_parent_id;

    // Form inputs for Contact Us
    public $contact_alamat, $contact_email, $contact_fax, $contact_jam_buka, $contact_iframe, $contact_facebook, $contact_twitter, $contact_tiktok, $contact_no_wa, $contact_text_wa, $contact_logo;
    public $active_template = 'default';

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'banner');
        $this->loadSingletons();
    }

    public function loadSingletons()
    {
        // About Us Singleton
        $about = HomeAboutUs::first();
        if ($about) {
            $this->about_title = $about->title;
            $this->about_description = $about->description;
            $this->about_image_1 = $about->image_1;
            $this->about_image_2 = $about->image_2;
            $this->about_video = $about->video;
        }

        // Tagline Singleton
        $tagline = HomeTagline::first();
        if ($tagline) {
            $this->tagline_title = $tagline->title_tagline;
            $this->tagline_keterangan = $tagline->keterangan_tagline;
            $this->tagline_wa = $tagline->wa_tagline;
        }

        // Contact Us Singleton
        $contact = HomeContactUs::first();
        if ($contact) {
            $this->contact_alamat = $contact->alamat_kantor;
            $this->contact_email = $contact->email;
            $this->contact_fax = $contact->fax;
            $this->contact_jam_buka = $contact->jam_buka;
            $this->contact_iframe = $contact->iframe;
            $this->contact_facebook = $contact->facebook;
            $this->contact_twitter = $contact->twitter;
            $this->contact_tiktok = $contact->tiktok;
            $this->contact_no_wa = $contact->no_wa;
            $this->contact_text_wa = $contact->text_wa;
            $this->contact_logo = $contact->logo;
            $this->active_template = $contact->template ?: 'default';
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->isOpen = false;
        $this->isEditMode = false;
        $this->editId = null;
        $this->resetUploads();
    }

    public function resetUploads()
    {
        $this->uploadImage = null;
        $this->uploadImage1 = null;
        $this->uploadImage2 = null;
        $this->uploadLogo = null;
        $this->uploadVideo = null;
    }

    public function openModal()
    {
        $this->isOpen = true;
        $this->isEditMode = false;
        $this->editId = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->resetUploads();
        $this->banner_title = '';
        $this->banner_short_description = '';
        $this->banner_status = 'active';

        $this->service_title = '';
        $this->service_short_description = '';
        $this->service_status = 'active';
        $this->service_parent_id = HomeServiceParent::first()?->id;

        $this->value_title = '';
        $this->value_short_description = '';
        $this->value_status = 'active';
        $this->value_parent_id = HomeValueParent::first()?->id;

        $this->gallery_title = '';
        $this->gallery_parent_id = HomeGalleryParent::first()?->id;

        $this->client_title = '';
        $this->client_link = '';
        $this->client_status = 'active';
        $this->client_parent_id = HomeClientParent::first()?->id;

        $this->testimoni_title = '';
        $this->testimoni_customer = '';
        $this->testimoni_desc = '';
        $this->testimoni_status = 'active';
        $this->testimoni_parent_id = HomeTestimoniParent::first()?->id;
    }

    // Save About Us (Singleton CRUD)
    public function saveAboutUs()
    {
        $this->validate([
            'about_title' => 'required',
            'about_description' => 'required',
        ]);

        $about = HomeAboutUs::first() ?: new HomeAboutUs();
        $about->title = $this->about_title;
        $about->description = $this->about_description;

        if ($this->uploadImage1) {
            $about->image_1 = $this->uploadImage1->store('home', 'public');
        }
        if ($this->uploadImage2) {
            $about->image_2 = $this->uploadImage2->store('home', 'public');
        }
        if ($this->uploadVideo) {
            $about->video = $this->uploadVideo->store('home', 'public');
        }

        $about->save();
        $this->loadSingletons();
        session()->flash('success', 'About Us updated successfully.');
    }

    // Save Tagline (Singleton CRUD)
    public function saveTagline()
    {
        $this->validate([
            'tagline_title' => 'required',
            'tagline_keterangan' => 'required',
            'tagline_wa' => 'required',
        ]);

        $tagline = HomeTagline::first() ?: new HomeTagline();
        $tagline->title_tagline = $this->tagline_title;
        $tagline->keterangan_tagline = $this->tagline_keterangan;
        $tagline->wa_tagline = $this->tagline_wa;
        $tagline->save();

        $this->loadSingletons();
        session()->flash('success', 'Tagline updated successfully.');
    }

    // Save Contact Us (Singleton CRUD)
    public function saveContactUs()
    {
        $this->validate([
            'contact_alamat' => 'required',
            'contact_email' => 'required|email',
            'contact_fax' => 'required',
            'contact_jam_buka' => 'required',
            'contact_text_wa' => 'required',
        ]);

        $contact = HomeContactUs::first() ?: new HomeContactUs();
        $contact->alamat_kantor = $this->contact_alamat;
        $contact->email = $this->contact_email;
        $contact->fax = $this->contact_fax;
        $contact->jam_buka = $this->contact_jam_buka;
        $contact->iframe = $this->contact_iframe;
        $contact->facebook = $this->contact_facebook;
        $contact->twitter = $this->contact_twitter;
        $contact->tiktok = $this->contact_tiktok;
        $contact->no_wa = $this->contact_no_wa;
        $contact->text_wa = $this->contact_text_wa;

        if ($this->uploadLogo) {
            $contact->logo = $this->uploadLogo->store('home', 'public');
        } else if (!$contact->logo) {
            $contact->logo = 'default-logo.png';
        }

        $contact->save();
        $this->loadSingletons();
        session()->flash('success', 'Contact Us updated successfully.');
    }

    // Banner CRUD Methods
    public function saveBanner()
    {
        $rules = [
            'banner_title' => 'required',
            'banner_short_description' => 'required',
            'banner_status' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $banner = $this->isEditMode ? HomeBanner::find($this->editId) : new HomeBanner();
        $banner->title = $this->banner_title;
        $banner->short_description = $this->banner_short_description;
        $banner->status = $this->banner_status;

        if ($this->uploadImage) {
            $banner->image = $this->uploadImage->store('home', 'public');
        }

        $banner->save();
        $this->isOpen = false;
        session()->flash('success', 'Banner saved successfully.');
    }

    public function editBanner($id)
    {
        $banner = HomeBanner::findOrFail($id);
        $this->editId = $banner->id;
        $this->banner_title = $banner->title;
        $this->banner_short_description = $banner->short_description;
        $this->banner_status = $banner->status;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteBanner($id)
    {
        HomeBanner::destroy($id);
        session()->flash('success', 'Banner deleted.');
    }

    // Services CRUD Methods
    public function saveService()
    {
        $rules = [
            'service_title' => 'required',
            'service_short_description' => 'required',
            'service_status' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $service = $this->isEditMode ? HomeService::find($this->editId) : new HomeService();
        $service->title = $this->service_title;
        $service->short_description = $this->service_short_description;
        $service->status = $this->service_status;
        $service->parent_id = $this->service_parent_id ?: HomeServiceParent::first()?->id;

        if ($this->uploadImage) {
            $service->image = $this->uploadImage->store('home', 'public');
        }

        $service->save();
        $this->isOpen = false;
        session()->flash('success', 'Service saved successfully.');
    }

    public function editService($id)
    {
        $service = HomeService::findOrFail($id);
        $this->editId = $service->id;
        $this->service_title = $service->title;
        $this->service_short_description = $service->short_description;
        $this->service_status = $service->status;
        $this->service_parent_id = $service->parent_id;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteService($id)
    {
        HomeService::destroy($id);
        session()->flash('success', 'Service deleted.');
    }

    // Value CRUD Methods
    public function saveValue()
    {
        $rules = [
            'value_title' => 'required',
            'value_short_description' => 'required',
            'value_status' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $value = $this->isEditMode ? HomeValue::find($this->editId) : new HomeValue();
        $value->title = $this->value_title;
        $value->short_description = $this->value_short_description;
        $value->status = $this->value_status;
        $value->parent_id = $this->value_parent_id ?: HomeValueParent::first()?->id;

        if ($this->uploadImage) {
            $value->image = $this->uploadImage->store('home', 'public');
        }

        $value->save();
        $this->isOpen = false;
        session()->flash('success', 'Value saved successfully.');
    }

    public function editValue($id)
    {
        $value = HomeValue::findOrFail($id);
        $this->editId = $value->id;
        $this->value_title = $value->title;
        $this->value_short_description = $value->short_description;
        $this->value_status = $value->status;
        $this->value_parent_id = $value->parent_id;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteValue($id)
    {
        HomeValue::destroy($id);
        session()->flash('success', 'Value deleted.');
    }

    // Gallery CRUD Methods
    public function saveGallery()
    {
        $rules = [
            'gallery_title' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $gallery = $this->isEditMode ? HomeGallery::find($this->editId) : new HomeGallery();
        $gallery->title = $this->gallery_title;
        $gallery->parent_id = $this->gallery_parent_id ?: HomeGalleryParent::first()?->id;

        if ($this->uploadImage) {
            $gallery->image = $this->uploadImage->store('home', 'public');
        }

        $gallery->save();
        $this->isOpen = false;
        session()->flash('success', 'Gallery image saved.');
    }

    public function editGallery($id)
    {
        $gallery = HomeGallery::findOrFail($id);
        $this->editId = $gallery->id;
        $this->gallery_title = $gallery->title;
        $this->gallery_parent_id = $gallery->parent_id;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteGallery($id)
    {
        HomeGallery::destroy($id);
        session()->flash('success', 'Gallery image deleted.');
    }

    // Client CRUD Methods
    public function saveClient()
    {
        $rules = [
            'client_title' => 'required',
            'client_status' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $client = $this->isEditMode ? HomeClient::find($this->editId) : new HomeClient();
        $client->title = $this->client_title;
        $client->link = $this->client_link;
        $client->status = $this->client_status;
        $client->parent_id = $this->client_parent_id ?: HomeClientParent::first()?->id;

        if ($this->uploadImage) {
            $client->image = $this->uploadImage->store('home', 'public');
        }

        $client->save();
        $this->isOpen = false;
        session()->flash('success', 'Client saved successfully.');
    }

    public function editClient($id)
    {
        $client = HomeClient::findOrFail($id);
        $this->editId = $client->id;
        $this->client_title = $client->title;
        $this->client_link = $client->link;
        $this->client_status = $client->status;
        $this->client_parent_id = $client->parent_id;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteClient($id)
    {
        HomeClient::destroy($id);
        session()->flash('success', 'Client deleted.');
    }

    // Testimoni CRUD Methods
    public function saveTestimoni()
    {
        $rules = [
            'testimoni_title' => 'required',
            'testimoni_customer' => 'required',
            'testimoni_desc' => 'required',
            'testimoni_status' => 'required',
        ];
        if (!$this->isEditMode) {
            $rules['uploadImage'] = 'required|image|max:5048';
        }
        $this->validate($rules);

        $testimoni = $this->isEditMode ? HomeTestimoni::find($this->editId) : new HomeTestimoni();
        $testimoni->title = $this->testimoni_title;
        $testimoni->nama_customer = $this->testimoni_customer;
        $testimoni->short_description = $this->testimoni_desc;
        $testimoni->status = $this->testimoni_status;
        $testimoni->parent_id = $this->testimoni_parent_id ?: HomeTestimoniParent::first()?->id;

        if ($this->uploadImage) {
            $testimoni->image = $this->uploadImage->store('home', 'public');
        }

        $testimoni->save();
        $this->isOpen = false;
        session()->flash('success', 'Testimoni saved successfully.');
    }

    public function editTestimoni($id)
    {
        $testimoni = HomeTestimoni::findOrFail($id);
        $this->editId = $testimoni->id;
        $this->testimoni_title = $testimoni->title;
        $this->testimoni_customer = $testimoni->nama_customer;
        $this->testimoni_desc = $testimoni->short_description;
        $this->testimoni_status = $testimoni->status;
        $this->testimoni_parent_id = $testimoni->parent_id;
        $this->isEditMode = true;
        $this->isOpen = true;
    }

    public function deleteTestimoni($id)
    {
        HomeTestimoni::destroy($id);
        session()->flash('success', 'Testimoni deleted.');
    }

    public function saveTemplate()
    {
        $contact = HomeContactUs::first() ?: new HomeContactUs();
        $contact->template = $this->active_template;
        if (!$contact->alamat_kantor) $contact->alamat_kantor = 'Office address';
        if (!$contact->email) $contact->email = 'admin@example.com';
        if (!$contact->fax) $contact->fax = '-';
        if (!$contact->jam_buka) $contact->jam_buka = '09:00 - 17:00';
        if (!$contact->text_wa) $contact->text_wa = 'Hello';
        if (!$contact->logo) $contact->logo = 'default-logo.png';

        $contact->save();
        $this->loadSingletons();
        session()->flash('success', 'Homepage template updated successfully.');
    }

    public function render()
    {
        return view('livewire.content-manager', [
            'banners' => HomeBanner::latest()->get(),
            'services' => HomeService::latest()->get(),
            'values' => HomeValue::latest()->get(),
            'galleries' => HomeGallery::latest()->get(),
            'clients' => HomeClient::latest()->get(),
            'testimonis' => HomeTestimoni::latest()->get(),
        ])->layout('layouts.app');
    }
}
