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
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn([
                'harga_barang',
                'ongkir_supplier_to_forwarder',
                'tax_refund',
                'ongkir_china_to_indonesia',
                'pajak_impor',
                'margin'
            ]);
            $table->integer('persediaan_awal')->default(0)->after('nama_barang');
            $table->integer('pembelian_bersih')->default(0)->after('persediaan_awal');
            $table->integer('persediaan_akhir')->default(0)->after('pembelian_bersih');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->integer('harga_barang')->default(0);
            $table->integer('ongkir_supplier_to_forwarder')->default(0);
            $table->integer('tax_refund')->default(0);
            $table->integer('ongkir_china_to_indonesia')->default(0);
            $table->integer('pajak_impor')->default(0);
            $table->integer('margin')->default(0);
            $table->dropColumn(['persediaan_awal', 'pembelian_bersih', 'persediaan_akhir']);
        });
    }
};
