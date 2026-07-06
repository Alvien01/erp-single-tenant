<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\TaxInvoice;
use App\Models\WithholdingTax;
use App\Models\TaxFiling;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TaxFilingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tax_manager_renders_and_lists_filings(): void
    {
        $user = User::factory()->create();

        // Create dummy filing
        $filing = TaxFiling::create([
            'tax_type' => 'ppn',
            'period' => '2026-06',
            'amount' => 500000,
            'filing_date' => '2026-06-30',
            'ntpn' => '1234567890ABCDEF',
            'status' => 'filed',
            'notes' => 'Test PPN filing'
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->set('activeTab', 'filings')
            ->assertSee('PPN')
            ->assertSee('2026-06')
            ->assertSee('1234567890ABCDEF');
    }

    public function test_can_create_and_save_tax_filing(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->call('createFiling')
            ->set('filing_tax_type', 'pph21')
            ->set('filing_period', '2026-06')
            ->set('filing_amount', 150000)
            ->set('filing_date', '2026-06-25')
            ->set('filing_ntpn', 'NTPN987654')
            ->set('filing_status', 'filed')
            ->set('filing_notes', 'Monthly employee tax')
            ->call('saveFiling')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tax_filings', [
            'tax_type' => 'pph21',
            'period' => '2026-06',
            'amount' => 150000.00,
            'ntpn' => 'NTPN987654',
            'status' => 'filed'
        ]);
    }

    public function test_suggested_ppn_amount_calculation(): void
    {
        $user = User::factory()->create();

        // Create tax invoices for June 2026
        // PPN Masukan (Input Tax)
        TaxInvoice::create([
            'invoice_number' => 'FP-20260601-M001',
            'type' => 'masukan',
            'date' => '2026-06-05',
            'dpp' => 1000000,
            'ppn' => 110000,
            'status' => 'approved'
        ]);

        // PPN Keluaran (Output Tax)
        TaxInvoice::create([
            'invoice_number' => 'FP-20260610-K001',
            'type' => 'keluaran',
            'date' => '2026-06-12',
            'dpp' => 3000000,
            'ppn' => 330000,
            'status' => 'approved'
        ]);

        // Net PPN should be Keluaran (330,000) - Masukan (110,000) = 220,000
        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->set('filing_tax_type', 'ppn')
            ->set('filing_period', '2026-06')
            ->call('calculateSuggestedFilingAmount')
            ->assertSet('suggested_amount', 220000)
            ->call('applySuggestedAmount')
            ->assertSet('filing_amount', 220000);
    }

    public function test_suggested_pph_amount_calculation(): void
    {
        $user = User::factory()->create();

        // Create withholding tax for June 2026
        $wht = new WithholdingTax([
            'type' => 'pph23',
            'amount' => 75000,
            'status' => 'unpaid'
        ]);
        $wht->created_at = '2026-06-15 10:00:00';
        $wht->save();

        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->set('filing_tax_type', 'pph23')
            ->set('filing_period', '2026-06')
            ->call('calculateSuggestedFilingAmount')
            ->assertSet('suggested_amount', 75000)
            ->call('applySuggestedAmount')
            ->assertSet('filing_amount', 75000);
    }

    public function test_can_edit_and_update_tax_filing(): void
    {
        $user = User::factory()->create();

        $filing = TaxFiling::create([
            'tax_type' => 'pph25',
            'period' => '2026-05',
            'amount' => 450000,
            'filing_date' => '2026-05-20',
            'status' => 'draft'
        ]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->call('editFiling', $filing->id)
            ->assertSet('filing_amount', 450000)
            ->set('filing_status', 'filed')
            ->set('filing_ntpn', 'NTPN112233')
            ->call('saveFiling')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('tax_filings', [
            'id' => $filing->id,
            'status' => 'filed',
            'ntpn' => 'NTPN112233'
        ]);
    }

    public function test_can_delete_tax_filing(): void
    {
        $user = User::factory()->create();

        $filing = TaxFiling::create([
            'tax_type' => 'ppn',
            'period' => '2026-04',
            'amount' => 120000,
            'filing_date' => '2026-04-30',
            'status' => 'draft'
        ]);

        $this->assertDatabaseHas('tax_filings', ['id' => $filing->id]);

        Livewire::actingAs($user)
            ->test(\App\Livewire\TaxManager::class)
            ->call('deleteFiling', $filing->id);

        $this->assertDatabaseMissing('tax_filings', ['id' => $filing->id]);
    }
}
