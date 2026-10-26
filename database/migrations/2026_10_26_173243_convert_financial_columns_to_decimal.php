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
        // 1. Convert integer/bigInteger financial fields in hpp_calculations to decimal(18, 2)
        if (Schema::hasTable('hpp_calculations')) {
            Schema::table('hpp_calculations', function (Blueprint $table) {
                if (Schema::hasColumn('hpp_calculations', 'harga_barang')) {
                    $table->decimal('harga_barang', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_forwarder')) {
                    $table->decimal('ongkir_forwarder', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'tax_refund')) {
                    $table->decimal('tax_refund', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_indonesia')) {
                    $table->decimal('ongkir_indonesia', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ppn_value')) {
                    $table->decimal('ppn_value', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pph_value')) {
                    $table->decimal('pph_value', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'keuntungan_value')) {
                    $table->decimal('keuntungan_value', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'harga_jual')) {
                    $table->decimal('harga_jual', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'total_hpp')) {
                    $table->decimal('total_hpp', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'persediaan_awal')) {
                    $table->decimal('persediaan_awal', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pembelian_bersih')) {
                    $table->decimal('pembelian_bersih', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'persediaan_akhir')) {
                    $table->decimal('persediaan_akhir', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_supplier_to_forwarder')) {
                    $table->decimal('ongkir_supplier_to_forwarder', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_china_to_indonesia')) {
                    $table->decimal('ongkir_china_to_indonesia', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pajak_impor')) {
                    $table->decimal('pajak_impor', 18, 2)->default(0)->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'margin')) {
                    $table->decimal('margin', 18, 2)->default(0)->nullable()->change();
                }
            });
        }

        // 2. Convert employee allowances in employees to decimal(15, 2)
        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'transport_allowance')) {
                    $table->decimal('transport_allowance', 15, 2)->default(30000)->change();
                }
                if (Schema::hasColumn('employees', 'meal_allowance')) {
                    $table->decimal('meal_allowance', 15, 2)->default(25000)->change();
                }
            });
        }

        // 3. Convert leads probability to decimal(5, 2)
        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'probability')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->decimal('probability', 5, 2)->default(10.00)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('hpp_calculations')) {
            Schema::table('hpp_calculations', function (Blueprint $table) {
                if (Schema::hasColumn('hpp_calculations', 'harga_barang')) {
                    $table->bigInteger('harga_barang')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_forwarder')) {
                    $table->bigInteger('ongkir_forwarder')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'tax_refund')) {
                    $table->bigInteger('tax_refund')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_indonesia')) {
                    $table->bigInteger('ongkir_indonesia')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ppn_value')) {
                    $table->bigInteger('ppn_value')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pph_value')) {
                    $table->bigInteger('pph_value')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'keuntungan_value')) {
                    $table->bigInteger('keuntungan_value')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'harga_jual')) {
                    $table->bigInteger('harga_jual')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'total_hpp')) {
                    $table->integer('total_hpp')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'persediaan_awal')) {
                    $table->integer('persediaan_awal')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pembelian_bersih')) {
                    $table->integer('pembelian_bersih')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'persediaan_akhir')) {
                    $table->integer('persediaan_akhir')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_supplier_to_forwarder')) {
                    $table->integer('ongkir_supplier_to_forwarder')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'ongkir_china_to_indonesia')) {
                    $table->integer('ongkir_china_to_indonesia')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'pajak_impor')) {
                    $table->integer('pajak_impor')->nullable()->change();
                }
                if (Schema::hasColumn('hpp_calculations', 'margin')) {
                    $table->integer('margin')->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('employees')) {
            Schema::table('employees', function (Blueprint $table) {
                if (Schema::hasColumn('employees', 'transport_allowance')) {
                    $table->integer('transport_allowance')->default(30000)->change();
                }
                if (Schema::hasColumn('employees', 'meal_allowance')) {
                    $table->integer('meal_allowance')->default(25000)->change();
                }
            });
        }

        if (Schema::hasTable('leads') && Schema::hasColumn('leads', 'probability')) {
            Schema::table('leads', function (Blueprint $table) {
                $table->integer('probability')->default(10)->change();
            });
        }
    }
};
