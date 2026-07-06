<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tax_filings', function (Blueprint $table) {
            $table->id();
            $table->string('tax_type'); // e.g. ppn, pph21, pph23, pph25, pph29
            $table->string('period'); // e.g. 2026-06
            $table->decimal('amount', 15, 2)->default(0);
            $table->date('filing_date');
            $table->string('ntpn')->nullable(); // BPE or NTPN receipt code
            $table->string('status')->default('draft'); // draft, filed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_filings');
    }
};
