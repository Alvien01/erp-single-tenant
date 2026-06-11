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
        $contact = HomeContactUs::query()->first();
        $about = HomeAboutUs::query()->first();
        $activeBannersCount = HomeBanner::query()->where('status', 'active')->count();

        // Redirect to login if initial config/content is not set or empty
        if (!$contact || !$about || $activeBannersCount === 0) {
            return redirect()->route('login');
        }

        $template = $contact->template ?? 'default';

        if (!view()->exists("themes.{$template}")) {
            $template = 'default';
        }

        return view('welcome', [
            'template' => $template,
            'banners' => HomeBanner::query()->where('status', 'active')->get(),
            'about' => $about,
            'service_parent' => HomeServiceParent::query()->first(),
            'services' => HomeService::query()->where('status', 'active')->get(),
            'value_parent' => HomeValueParent::query()->first(),
            'values' => HomeValue::query()->where('status', 'active')->get(),
            'gallery_parent' => HomeGalleryParent::query()->first(),
            'galleries' => HomeGallery::query()->get(),
            'client_parent' => HomeClientParent::query()->first(),
            'clients' => HomeClient::query()->where('status', 'active')->get(),
            'tagline' => HomeTagline::query()->first(),
            'testimoni_parent' => HomeTestimoniParent::query()->first(),
            'testimonis' => HomeTestimoni::query()->where('status', 'active')->get(),
            'contact' => $contact,
        ]);
    }
}
