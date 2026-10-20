<?php

namespace Tests\Feature;

use App\Models\HomeContactUs;
use App\Models\HomeAboutUs;
use App\Models\HomeBanner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        HomeContactUs::create([
            'alamat_kantor' => 'Jl. Test No. 123',
            'email' => 'admin@test.com',
            'fax' => '123-456',
            'jam_buka' => '08:00 - 17:00',
            'template' => 'default',
            'text_wa' => 'Hubungi Kami',
            'logo' => 'logo.png',
        ]);
        HomeAboutUs::create([
            'title' => 'About Us',
            'description' => 'About Us Description',
        ]);
        HomeBanner::create([
            'title' => 'Banner Title',
            'short_description' => 'Banner Description',
            'image' => 'banner.jpg',
            'status' => 'active',
        ]);

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
