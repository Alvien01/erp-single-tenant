<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HomeBanner;
use App\Models\HomeAboutUs;
use App\Models\HomeServiceParent;
use App\Models\HomeService;
use App\Models\HomeValueParent;
use App\Models\HomeValue;
use App\Models\HomeGalleryParent;
use App\Models\HomeGallery;
use App\Models\HomeClientParent;
use App\Models\HomeClient;
use App\Models\HomeTagline;
use App\Models\HomeTestimoniParent;
use App\Models\HomeTestimoni;
use App\Models\HomeContactUs;

class HomeController extends Controller
{
    public function index()
    {
        $contact = HomeContactUs::first();
        $template = $contact->template ?? 'default';

        if (!view()->exists("themes.{$template}")) {
            $template = 'default';
        }

        return view('welcome', [
            'template' => $template,
            'banners' => HomeBanner::where('status', 'active')->get(),
            'about' => HomeAboutUs::first(),
            'service_parent' => HomeServiceParent::first(),
            'services' => HomeService::where('status', 'active')->get(),
            'value_parent' => HomeValueParent::first(),
            'values' => HomeValue::where('status', 'active')->get(),
            'gallery_parent' => HomeGalleryParent::first(),
            'galleries' => HomeGallery::get(),
            'client_parent' => HomeClientParent::first(),
            'clients' => HomeClient::where('status', 'active')->get(),
            'tagline' => HomeTagline::first(),
            'testimoni_parent' => HomeTestimoniParent::first(),
            'testimonis' => HomeTestimoni::where('status', 'active')->get(),
            'contact' => $contact,
        ]);
    }
}
