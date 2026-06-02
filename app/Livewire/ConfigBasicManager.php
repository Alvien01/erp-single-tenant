<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\HomeServiceParent;
use App\Models\HomeValueParent;
use App\Models\HomeGalleryParent;
use App\Models\HomeClientParent;
use App\Models\HomeTestimoniParent;
use App\Models\HomeBanner;
use App\Models\HomeAboutUs;
use App\Models\HomeTagline;
use App\Models\HomeContactUs;

class ConfigBasicManager extends Component
{
    use WithFileUploads;

    public $activeTab = 'banner';

    // Banner parent config (we can use first banner as base or custom fields)
    public $banner_p_title, $banner_p_desc;

    // About Us config
    public $about_p_title, $about_p_desc;

    // Services Parent fields
    public $service_p_title, $service_p_desc;

    // Value Parent fields
    public $value_p_title, $value_p_desc, $value_p_image, $uploadValuePImage;

    // Gallery Parent fields
    public $gallery_p_title, $gallery_p_desc;

    // Client Parent fields
    public $client_p_title, $client_p_desc;

    // Testimoni Parent fields
    public $testimoni_p_title, $testimoni_p_desc;

    // Tagline fields
    public $tagline_p_title, $tagline_p_desc, $tagline_p_wa;

    // Contact Us fields
    public $contact_p_alamat, $contact_p_email, $contact_p_fax, $contact_p_jam, $contact_p_logo, $uploadLogo;

    // News placeholder config
    public $news_p_title = 'Latest News', $news_p_desc = 'Stay updated with our latest articles and business news.';

    public function mount()
    {
        $this->activeTab = request()->query('tab', 'banner');
        $this->loadData();
    }

    public function loadData()
    {
        // Banner
        $banner = HomeBanner::first();
        if ($banner) {
            $this->banner_p_title = $banner->title;
            $this->banner_p_desc = $banner->short_description;
        }

        // About Us
        $about = HomeAboutUs::first();
        if ($about) {
            $this->about_p_title = $about->title;
            $this->about_p_desc = $about->description;
        }

        // Services Parent
        $service = HomeServiceParent::first() ?: new HomeServiceParent();
        $this->service_p_title = $service->title ?: 'Our Professional Services';
        $this->service_p_desc = $service->description ?: 'We provide premium enterprise services to grow your business.';

        // Value Parent
        $val = HomeValueParent::first() ?: new HomeValueParent();
        $this->value_p_title = $val->title ?: 'Our Core Values';
        $this->value_p_desc = $val->description ?: 'Driven by integrity, built on quality.';
        $this->value_p_image = $val->image;

        // Gallery Parent
        $gal = HomeGalleryParent::first() ?: new HomeGalleryParent();
        $this->gallery_p_title = $gal->title ?: 'Product Gallery';
        $this->gallery_p_desc = $gal->description ?: 'Explore our state of the art products and warehouse facilities.';

        // Client Parent
        $cli = HomeClientParent::first() ?: new HomeClientParent();
        $this->client_p_title = $cli->title ?: 'Trusted by Industry Leaders';
        $this->client_p_desc = $cli->description ?: 'Over 100+ organizations rely on our ERP enterprise platform.';

        // Testimoni Parent
        $test = HomeTestimoniParent::first() ?: new HomeTestimoniParent();
        $this->testimoni_p_title = $test->title ?: 'What Our Customers Say';
        $this->testimoni_p_desc = $test->description ?: 'Hear stories of success directly from our global clients.';

        // Tagline
        $tag = HomeTagline::first();
        if ($tag) {
            $this->tagline_p_title = $tag->title_tagline;
            $this->tagline_p_desc = $tag->keterangan_tagline;
            $this->tagline_p_wa = $tag->wa_tagline;
        }

        // Contact
        $con = HomeContactUs::first();
        if ($con) {
            $this->contact_p_alamat = $con->alamat_kantor;
            $this->contact_p_email = $con->email;
            $this->contact_p_fax = $con->fax;
            $this->contact_p_jam = $con->jam_buka;
            $this->contact_p_logo = $con->logo;
        }
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->uploadValuePImage = null;
        $this->uploadLogo = null;
    }

    public function saveConfig()
    {
        if ($this->activeTab === 'banner') {
            $banner = HomeBanner::first() ?: new HomeBanner();
            $banner->title = $this->banner_p_title;
            $banner->short_description = $this->banner_p_desc;
            if (!$banner->image) $banner->image = 'default.png';
            $banner->save();
        }

        if ($this->activeTab === 'about') {
            $about = HomeAboutUs::first() ?: new HomeAboutUs();
            $about->title = $this->about_p_title;
            $about->description = $this->about_p_desc;
            $about->save();
        }

        if ($this->activeTab === 'service') {
            $service = HomeServiceParent::first() ?: new HomeServiceParent();
            $service->title = $this->service_p_title;
            $service->description = $this->service_p_desc;
            $service->save();
        }

        if ($this->activeTab === 'value') {
            $val = HomeValueParent::first() ?: new HomeValueParent();
            $val->title = $this->value_p_title;
            $val->description = $this->value_p_desc;
            if ($this->uploadValuePImage) {
                $val->image = $this->uploadValuePImage->store('home', 'public');
            }
            $val->save();
        }

        if ($this->activeTab === 'gallery') {
            $gal = HomeGalleryParent::first() ?: new HomeGalleryParent();
            $gal->title = $this->gallery_p_title;
            $gal->description = $this->gallery_p_desc;
            $gal->save();
        }

        if ($this->activeTab === 'client') {
            $cli = HomeClientParent::first() ?: new HomeClientParent();
            $cli->title = $this->client_p_title;
            $cli->description = $this->client_p_desc;
            $cli->save();
        }

        if ($this->activeTab === 'testimoni') {
            $test = HomeTestimoniParent::first() ?: new HomeTestimoniParent();
            $test->title = $this->testimoni_p_title;
            $test->description = $this->testimoni_p_desc;
            $test->save();
        }

        if ($this->activeTab === 'tagline') {
            $tag = HomeTagline::first() ?: new HomeTagline();
            $tag->title_tagline = $this->tagline_p_title;
            $tag->keterangan_tagline = $this->tagline_p_desc;
            $tag->wa_tagline = $this->tagline_p_wa ?: '0';
            $tag->save();
        }

        if ($this->activeTab === 'contact') {
            $con = HomeContactUs::first() ?: new HomeContactUs();
            $con->alamat_kantor = $this->contact_p_alamat;
            $con->email = $this->contact_p_email;
            $con->fax = $this->contact_p_fax;
            $con->jam_buka = $this->contact_p_jam;
            $con->text_wa = 'Hello';
            if ($this->uploadLogo) {
                $con->logo = $this->uploadLogo->store('home', 'public');
            } else if (!$con->logo) {
                $con->logo = 'default-logo.png';
            }
            $con->save();
        }

        $this->loadData();
        session()->flash('success', 'Homepage basic configuration saved successfully.');
    }

    public function render()
    {
        return view('livewire.config-basic-manager')->layout('layouts.app');
    }
}
