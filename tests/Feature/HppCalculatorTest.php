<?php

namespace Tests\Feature;

use App\Livewire\HppCalculatorManager;
use App\Models\HppCalculation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HppCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_render_hpp_calculator(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/hpp-calculator');
        $response->assertStatus(200);
    }

    public function test_can_calculate_umum_hpp(): void
    {
        Livewire::test(HppCalculatorManager::class)
            ->set('active_tab', 'umum')
            ->set('persediaan_awal', 50000)
            ->set('pembelian_bersih', 150000)
            ->set('persediaan_akhir', 30000)
            ->call('calculateHpp')
            ->assertSet('total_hpp', 170000);
    }

    public function test_can_calculate_internal_hpp_with_pph_final_and_profit_nominal(): void
    {
        // Cost: 80,000 + 10,000 - 5,000 + 15,000 = 100,000
        // PPN: 100,000 * 11% = 11,000
        // Cost with PPN: 111,000
        // Profit: 20,000
        // PPh Final: 0.5% of selling price
        // Selling price: (111,000 + 20,000) / 0.995 = 131,658.29 -> 131,658
        // PPh: 131,658 * 0.005 = 658.29 -> 658
        // Total HPP (Cost + PPh): 111,000 + 658 = 111,658
        Livewire::test(HppCalculatorManager::class)
            ->set('active_tab', 'internal')
            ->set('harga_barang', 80000)
            ->set('ongkir_forwarder', 10000)
            ->set('tax_refund', 5000)
            ->set('ongkir_indonesia', 15000)
            ->set('ppn_rate', 11.0)
            ->set('pph_type', 'final_0.5')
            ->set('keuntungan_type', 'nominal')
            ->set('keuntungan_rate', 20000)
            ->call('calculateHpp')
            ->assertSet('ppn_value', 11000)
            ->assertSet('pph_value', 658)
            ->assertSet('keuntungan_value', 20000)
            ->assertSet('harga_jual', 131658)
            ->assertSet('total_hpp', 111658);
    }

    public function test_can_calculate_internal_hpp_with_pph_badan_and_profit_margin(): void
    {
        // Cost: 80,000 + 0 - 0 + 0 = 80,000
        // PPN: 80,000 * 0% = 0
        // Cost with PPN: 80,000
        // Profit rate: 20% margin (from Selling Price)
        // PPh Badan: 11% (from Laba/Keuntungan)
        // Selling Price: Cost / (1 - 1.11 * margin) = 80,000 / (1 - 1.11 * 0.2) = 80,000 / (1 - 0.222) = 80,000 / 0.778 = 102,827.76 -> 102,828
        // Keuntungan: 102,828 * 0.2 = 20,565.6 -> 20,566
        // PPh: 20,566 * 0.11 = 2262.26 -> 2,262
        // Total HPP (Cost + PPh): 80,000 + 2,262 = 82,262
        Livewire::test(HppCalculatorManager::class)
            ->set('active_tab', 'internal')
            ->set('harga_barang', 80000)
            ->set('ongkir_forwarder', 0)
            ->set('tax_refund', 0)
            ->set('ongkir_indonesia', 0)
            ->set('ppn_rate', 0.0)
            ->set('pph_type', 'badan_11')
            ->set('keuntungan_type', 'margin')
            ->set('keuntungan_rate', 20.0)
            ->call('calculateHpp')
            ->assertSet('ppn_value', 0)
            ->assertSet('keuntungan_value', 20566)
            ->assertSet('pph_value', 2262)
            ->assertSet('harga_jual', 102828)
            ->assertSet('total_hpp', 82262);
    }
}
