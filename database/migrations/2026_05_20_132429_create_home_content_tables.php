<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Data Banner (CRUD)
        Schema::create('home_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('short_description');
            $table->string('image');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Data About us (CRUD)
        Schema::create('home_about_us', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image_1')->nullable();
            $table->string('image_2')->nullable();
            $table->string('video')->nullable();
            $table->timestamps();
        });

        // Data Our Services Parent
        Schema::create('home_service_parents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Data Our Services Child
        Schema::create('home_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('home_service_parents')->cascadeOnDelete();
            $table->string('title');
            $table->text('short_description');
            $table->string('image');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Data Our Value Parent
        Schema::create('home_value_parents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Data Our Value Child
        Schema::create('home_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('home_value_parents')->cascadeOnDelete();
            $table->string('title');
            $table->text('short_description');
            $table->string('image');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Data Gallery Parent
        Schema::create('home_gallery_parents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Data Gallery Child
        Schema::create('home_galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('home_gallery_parents')->cascadeOnDelete();
            $table->string('title');
            $table->string('image');
            $table->timestamps();
        });

        // Data Our Client Parent
        Schema::create('home_client_parents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Data Our Client Child
        Schema::create('home_clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('home_client_parents')->cascadeOnDelete();
            $table->string('title');
            $table->string('link')->nullable();
            $table->string('image');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Data Tagline
        Schema::create('home_taglines', function (Blueprint $table) {
            $table->id();
            $table->string('title_tagline');
            $table->string('keterangan_tagline');
            $table->string('wa_tagline');
            $table->timestamps();
        });

        // Data Testimoni Parent
        Schema::create('home_testimoni_parents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->timestamps();
        });

        // Data Testimoni Child
        Schema::create('home_testimonis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('home_testimoni_parents')->cascadeOnDelete();
            $table->string('title');
            $table->string('nama_customer');
            $table->text('short_description');
            $table->string('image');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // Data Contact Us
        Schema::create('home_contact_us', function (Blueprint $table) {
            $table->id();
            $table->string('alamat_kantor');
            $table->string('email');
            $table->string('fax');
            $table->string('jam_buka');
            $table->text('iframe')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('tiktok')->nullable();
            $table->string('no_wa')->nullable();
            $table->string('text_wa');
            $table->string('logo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_contact_us');
        Schema::dropIfExists('home_testimonis');
        Schema::dropIfExists('home_testimoni_parents');
        Schema::dropIfExists('home_taglines');
        Schema::dropIfExists('home_clients');
        Schema::dropIfExists('home_client_parents');
        Schema::dropIfExists('home_galleries');
        Schema::dropIfExists('home_gallery_parents');
        Schema::dropIfExists('home_values');
        Schema::dropIfExists('home_value_parents');
        Schema::dropIfExists('home_services');
        Schema::dropIfExists('home_service_parents');
        Schema::dropIfExists('home_about_us');
        Schema::dropIfExists('home_banners');
    }
};
