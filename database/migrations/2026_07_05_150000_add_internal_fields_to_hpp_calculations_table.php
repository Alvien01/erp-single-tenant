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
            $table->string('calculation_type')->default('umum')->after('nama_barang');
            $table->bigInteger('harga_barang')->nullable()->after('calculation_type');
            $table->bigInteger('ongkir_forwarder')->nullable()->after('harga_barang');
            $table->bigInteger('tax_refund')->nullable()->after('ongkir_forwarder');
            $table->bigInteger('ongkir_indonesia')->nullable()->after('tax_refund');
            $table->decimal('ppn_rate', 5, 2)->nullable()->after('ongkir_indonesia');
            $table->bigInteger('ppn_value')->nullable()->after('ppn_rate');
            $table->string('pph_type')->nullable()->after('ppn_value');
            $table->bigInteger('pph_value')->nullable()->after('pph_type');
            $table->string('keuntungan_type')->nullable()->after('pph_value');
            $table->decimal('keuntungan_rate', 15, 2)->nullable()->after('keuntungan_type');
            $table->bigInteger('keuntungan_value')->nullable()->after('keuntungan_rate');
            $table->bigInteger('harga_jual')->nullable()->after('keuntungan_value');
            
            $table->integer('persediaan_awal')->nullable()->change();
            $table->integer('pembelian_bersih')->nullable()->change();
            $table->integer('persediaan_akhir')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hpp_calculations', function (Blueprint $table) {
            $table->dropColumn([
                'calculation_type',
                'harga_barang',
                'ongkir_forwarder',
                'tax_refund',
                'ongkir_indonesia',
                'ppn_rate',
                'ppn_value',
                'pph_type',
                'pph_value',
                'keuntungan_type',
                'keuntungan_rate',
                'keuntungan_value',
                'harga_jual',
            ]);
            $table->integer('persediaan_awal')->nullable(false)->change();
            $table->integer('pembelian_bersih')->nullable(false)->change();
            $table->integer('persediaan_akhir')->nullable(false)->change();
        });
    }
};
