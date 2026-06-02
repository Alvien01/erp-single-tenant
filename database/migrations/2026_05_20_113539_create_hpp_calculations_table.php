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
        Schema::create('hpp_calculations', function (Blueprint $table) {
            $table->id();
            $table->string('nama_barang');
            $table->integer('harga_barang');
            $table->integer('ongkir_supplier_to_forwarder');
            $table->integer('tax_refund');
            $table->integer('ongkir_china_to_indonesia');
            $table->integer('pajak_impor');
            $table->integer('margin');
            $table->integer('total_hpp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hpp_calculations');
    }
};
