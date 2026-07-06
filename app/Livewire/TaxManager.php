<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Tax;
use App\Models\TaxInvoice;
use App\Models\WithholdingTax;
use App\Models\ActivityLog;
use App\Models\Account;
use App\Models\Journal;
use App\Models\TaxFiling;
use Illuminate\Support\Facades\Auth;

class TaxManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'rates'; // rates, invoices, withholding, filings
    public $isOpen = false;
    public $modalType = ''; // 'tax', 'invoice', 'withholding', 'filing'

    // Tax form fields
    public $tax_id;
    public $name;
    public $rate = 0;
    public $type = 'sales';
    public $is_active = true;

    // Invoice form fields
    public $invoice_id;
    public $invoice_number;
    public $invoice_type = 'keluaran'; // masukan, keluaran
    public $invoice_date;
    public $invoice_dpp = 0;
    public $invoice_ppn = 0;
    public $invoice_status = 'draft';

    // Withholding form fields
    public $wht_id;
    public $wht_type = 'pph21'; // pph21, pph22, pph23, pph25, pph29
    public $wht_amount = 0;
    public $wht_reference_type;
    public $wht_reference_id;
    public $wht_status = 'unpaid';

    // Filing form fields
    public $filing_id;
    public $filing_tax_type = 'ppn'; // ppn, pph21, pph23, pph25, pph29
    public $filing_period;
    public $filing_amount = 0;
    public $filing_date;
    public $filing_ntpn;
    public $filing_status = 'draft'; // draft, filed
    public $filing_notes;
    public $suggested_amount = null;

    public function mount()
    {
        $this->invoice_date = now()->format('Y-m-d');
        $this->filing_date = now()->format('Y-m-d');
        $this->filing_period = now()->format('Y-m');
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetTaxFields();
        $this->resetInvoiceFields();
        $this->resetWithholdingFields();
        $this->resetFilingFields();
    }

    public function resetTaxFields()
    {
        $this->tax_id = null;
        $this->name = '';
        $this->rate = 0;
        $this->type = 'sales';
        $this->is_active = true;
    }

    public function resetInvoiceFields()
    {
        $this->invoice_id = null;
        $this->invoice_number = '';
        $this->invoice_type = 'keluaran';
        $this->invoice_date = now()->format('Y-m-d');
        $this->invoice_dpp = 0;
        $this->invoice_ppn = 0;
        $this->invoice_status = 'draft';
    }

    public function resetWithholdingFields()
    {
        $this->wht_id = null;
        $this->wht_type = 'pph21';
        $this->wht_amount = 0;
        $this->wht_reference_type = '';
        $this->wht_reference_id = null;
        $this->wht_status = 'unpaid';
    }

    // Tax Rates Management
    public function createTax()
    {
        $this->resetTaxFields();
        $this->openModal('tax');
    }

    public function editTax($id)
    {
        $tax = Tax::findOrFail($id);
        $this->tax_id = $tax->id;
        $this->name = $tax->name;
        $this->rate = $tax->rate;
        $this->type = $tax->type;
        $this->is_active = $tax->is_active;

        $this->openModal('tax');
    }

    public function saveTax()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0|max:100',
            'type' => 'required|string',
        ]);

        Tax::updateOrCreate(
            ['id' => $this->tax_id],
            [
                'name' => $this->name,
                'rate' => $this->rate,
                'type' => $this->type,
                'is_active' => $this->is_active,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Tax Management',
            'action' => $this->tax_id ? 'Update Tax' : 'Create Tax',
            'description' => 'Saved tax rule: ' . $this->name . ' with rate ' . $this->rate . '%'
        ]);

        session()->flash('success', 'Tax rule saved successfully.');
        $this->closeModal();
    }

    public function deleteTax($id)
    {
        Tax::findOrFail($id)->delete();
        session()->flash('success', 'Tax rule deleted successfully.');
    }

    public function toggleTaxStatus($id)
    {
        $tax = Tax::findOrFail($id);
        $tax->is_active = !$tax->is_active;
        $tax->save();

        session()->flash('success', 'Tax status updated.');
    }

    // PPN Invoices (Faktur Pajak) Management
    public function createInvoice()
    {
        $this->resetInvoiceFields();
        $this->invoice_number = 'FP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $this->openModal('invoice');
    }

    public function editInvoice($id)
    {
        $inv = TaxInvoice::findOrFail($id);
        $this->invoice_id = $inv->id;
        $this->invoice_number = $inv->invoice_number;
        $this->invoice_type = $inv->type;
        $this->invoice_date = $inv->date;
        $this->invoice_dpp = $inv->dpp;
        $this->invoice_ppn = $inv->ppn;
        $this->invoice_status = $inv->status;

        $this->openModal('invoice');
    }

    public function saveInvoice()
    {
        $this->validate([
            'invoice_number' => 'required|string|max:255|unique:tax_invoices,invoice_number,' . $this->invoice_id,
            'invoice_type' => 'required|in:masukan,keluaran',
            'invoice_date' => 'required|date',
            'invoice_dpp' => 'required|numeric|min:0',
            'invoice_ppn' => 'required|numeric|min:0',
            'invoice_status' => 'required|in:draft,submitted,approved',
        ]);

        $inv = TaxInvoice::updateOrCreate(
            ['id' => $this->invoice_id],
            [
                'invoice_number' => $this->invoice_number,
                'type' => $this->invoice_type,
                'date' => $this->invoice_date,
                'dpp' => $this->invoice_dpp,
                'ppn' => $this->invoice_ppn,
                'status' => $this->invoice_status,
            ]
        );

        // If approved, post to general ledger automatically
        if ($this->invoice_status === 'approved' && (!$this->invoice_id || TaxInvoice::find($this->invoice_id)->status !== 'approved')) {
            $this->postPpnJournal($inv);
        }

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Tax Management',
            'action' => $this->invoice_id ? 'Update Tax Invoice' : 'Create Tax Invoice',
            'description' => 'Saved PPN Invoice ' . $this->invoice_number . ' (' . $this->invoice_type . ') with PPN Rp ' . number_format($this->invoice_ppn, 2)
        ]);

        session()->flash('success', 'PPN Invoice saved successfully.');
        $this->closeModal();
    }

    private function postPpnJournal($inv)
    {
        // For PPN Keluaran (Output Tax from Sales): Debit Cash/Bank or AR, Credit PPN Keluaran Payable Account
        // For PPN Masukan (Input Tax from Purchases): Debit PPN Masukan Asset Account, Credit Cash/Bank or AP
        // Let's create or find accounts
        $ppnMasukanAcc = Account::firstOrCreate(
            ['code' => '1150'],
            ['name' => 'PPN Masukan', 'type' => 'asset', 'balance' => 0]
        );

        $ppnKeluaranAcc = Account::firstOrCreate(
            ['code' => '2150'],
            ['name' => 'PPN Keluaran', 'type' => 'liability', 'balance' => 0]
        );

        $cashAcc = Account::where('name', 'like', '%bank%')->orWhere('name', 'like', '%kas%')->first() 
            ?? Account::firstOrCreate(['code' => '1101'], ['name' => 'Kas / Bank', 'type' => 'asset', 'balance' => 0]);

        if ($inv->type === 'masukan') {
            // Debit PPN Masukan, Credit Cash/Bank
            Journal::create([
                'account_id' => $ppnMasukanAcc->id,
                'transaction_date' => $inv->date,
                'description' => 'PPN Masukan ' . $inv->invoice_number,
                'reference_number' => $inv->invoice_number,
                'amount' => $inv->ppn,
                'type' => 'debit',
                'journal_type' => 'adjustment',
            ]);
            Journal::create([
                'account_id' => $cashAcc->id,
                'transaction_date' => $inv->date,
                'description' => 'PPN Masukan ' . $inv->invoice_number,
                'reference_number' => $inv->invoice_number,
                'amount' => $inv->ppn,
                'type' => 'credit',
                'journal_type' => 'adjustment',
            ]);

            $ppnMasukanAcc->increment('balance', $inv->ppn);
            $cashAcc->decrement('balance', $inv->ppn);
        } else {
            // Debit Cash/Bank, Credit PPN Keluaran
            Journal::create([
                'account_id' => $cashAcc->id,
                'transaction_date' => $inv->date,
                'description' => 'PPN Keluaran ' . $inv->invoice_number,
                'reference_number' => $inv->invoice_number,
                'amount' => $inv->ppn,
                'type' => 'debit',
                'journal_type' => 'adjustment',
            ]);
            Journal::create([
                'account_id' => $ppnKeluaranAcc->id,
                'transaction_date' => $inv->date,
                'description' => 'PPN Keluaran ' . $inv->invoice_number,
                'reference_number' => $inv->invoice_number,
                'amount' => $inv->ppn,
                'type' => 'credit',
                'journal_type' => 'adjustment',
            ]);

            $cashAcc->increment('balance', $inv->ppn);
            $ppnKeluaranAcc->increment('balance', $inv->ppn);
        }
    }

    // Withholding Tax Management
    public function createWithholding()
    {
        $this->resetWithholdingFields();
        $this->openModal('withholding');
    }

    public function editWithholding($id)
    {
        $wht = WithholdingTax::findOrFail($id);
        $this->wht_id = $wht->id;
        $this->wht_type = $wht->type;
        $this->wht_amount = $wht->amount;
        $this->wht_reference_type = $wht->reference_type;
        $this->wht_reference_id = $wht->reference_id;
        $this->wht_status = $wht->status;

        $this->openModal('withholding');
    }

    public function saveWithholding()
    {
        $this->validate([
            'wht_type' => 'required|in:pph21,pph22,pph23,pph25,pph29',
            'wht_amount' => 'required|numeric|min:0',
            'wht_status' => 'required|in:unpaid,paid',
        ]);

        WithholdingTax::updateOrCreate(
            ['id' => $this->wht_id],
            [
                'type' => $this->wht_type,
                'amount' => $this->wht_amount,
                'reference_type' => $this->wht_reference_type,
                'reference_id' => $this->wht_reference_id,
                'status' => $this->wht_status,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Tax Management',
            'action' => $this->wht_id ? 'Update Withholding Tax' : 'Create Withholding Tax',
            'description' => 'Saved Withholding Tax (' . strtoupper($this->wht_type) . ') amount Rp ' . number_format($this->wht_amount, 2)
        ]);

        session()->flash('success', 'Withholding tax saved successfully.');
        $this->closeModal();
    }

    public function resetFilingFields()
    {
        $this->filing_id = null;
        $this->filing_tax_type = 'ppn';
        $this->filing_period = now()->format('Y-m');
        $this->filing_amount = 0;
        $this->filing_date = now()->format('Y-m-d');
        $this->filing_ntpn = '';
        $this->filing_status = 'draft';
        $this->filing_notes = '';
        $this->suggested_amount = null;
    }

    // Tax Filings Management
    public function createFiling()
    {
        $this->resetFilingFields();
        $this->calculateSuggestedFilingAmount();
        $this->openModal('filing');
    }

    public function editFiling($id)
    {
        $filing = TaxFiling::findOrFail($id);
        $this->filing_id = $filing->id;
        $this->filing_tax_type = $filing->tax_type;
        $this->filing_period = $filing->period;
        $this->filing_amount = $filing->amount;
        $this->filing_date = $filing->filing_date ? $filing->filing_date->format('Y-m-d') : now()->format('Y-m-d');
        $this->filing_ntpn = $filing->ntpn;
        $this->filing_status = $filing->status;
        $this->filing_notes = $filing->notes;

        $this->calculateSuggestedFilingAmount();
        $this->openModal('filing');
    }

    public function updatedFilingPeriod()
    {
        $this->calculateSuggestedFilingAmount();
    }

    public function updatedFilingTaxType()
    {
        $this->calculateSuggestedFilingAmount();
    }

    public function calculateSuggestedFilingAmount()
    {
        if (empty($this->filing_period)) {
            $this->suggested_amount = null;
            return;
        }

        if ($this->filing_tax_type === 'ppn') {
            $yearMonth = $this->filing_period;
            $startDate = $yearMonth . '-01';
            $endDate = date('Y-m-t', strtotime($startDate));

            $ppnMasukan = TaxInvoice::where('type', 'masukan')
                ->where('status', 'approved')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('ppn');

            $ppnKeluaran = TaxInvoice::where('type', 'keluaran')
                ->where('status', 'approved')
                ->whereBetween('date', [$startDate, $endDate])
                ->sum('ppn');

            $this->suggested_amount = max(0, $ppnKeluaran - $ppnMasukan);
        } else {
            $yearMonth = $this->filing_period;
            $startDate = $yearMonth . '-01 00:00:00';
            $endDate = date('Y-m-t', strtotime($startDate)) . ' 23:59:59';

            $this->suggested_amount = WithholdingTax::where('type', $this->filing_tax_type)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount');
        }
    }

    public function applySuggestedAmount()
    {
        if ($this->suggested_amount !== null) {
            $this->filing_amount = $this->suggested_amount;
        }
    }

    public function saveFiling()
    {
        $this->validate([
            'filing_tax_type' => 'required|string|in:ppn,pph21,pph23,pph25,pph29',
            'filing_period' => 'required|regex:/^\d{4}-\d{2}$/',
            'filing_amount' => 'required|numeric|min:0',
            'filing_date' => 'required|date',
            'filing_ntpn' => 'nullable|string|max:255',
            'filing_status' => 'required|in:draft,filed',
            'filing_notes' => 'nullable|string',
        ]);

        TaxFiling::updateOrCreate(
            ['id' => $this->filing_id],
            [
                'tax_type' => $this->filing_tax_type,
                'period' => $this->filing_period,
                'amount' => $this->filing_amount,
                'filing_date' => $this->filing_date,
                'ntpn' => $this->filing_ntpn,
                'status' => $this->filing_status,
                'notes' => $this->filing_notes,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Tax Management',
            'action' => $this->filing_id ? 'Update Tax Filing' : 'Create Tax Filing',
            'description' => 'Saved tax filing for ' . strtoupper($this->filing_tax_type) . ' Period ' . $this->filing_period . ' amount Rp ' . number_format($this->filing_amount, 2)
        ]);

        session()->flash('success', 'Tax filing saved successfully.');
        $this->closeModal();
    }

    public function deleteFiling($id)
    {
        TaxFiling::findOrFail($id)->delete();
        session()->flash('success', 'Tax filing deleted successfully.');
    }

    public function render()
    {
        $s = '%' . $this->search . '%';

        // Query Taxes
        $taxes = Tax::where('name', 'like', $s)
            ->orderBy('name')
            ->paginate(10, ['*'], 'taxPage');

        // Query PPN Invoices
        $invoices = TaxInvoice::where('invoice_number', 'like', $s)
            ->orderBy('date', 'desc')
            ->paginate(10, ['*'], 'invPage');

        // Query Withholding Taxes
        $withholding = WithholdingTax::where('type', 'like', $s)
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'whtPage');

        // Query Tax Filings
        $filings = TaxFiling::where('tax_type', 'like', $s)
            ->orWhere('period', 'like', $s)
            ->orderBy('period', 'desc')
            ->paginate(10, ['*'], 'filingPage');

        // Compute PPN Summary
        $ppnMasukanApproved = TaxInvoice::where('type', 'masukan')->where('status', 'approved')->sum('ppn');
        $ppnKeluaranApproved = TaxInvoice::where('type', 'keluaran')->where('status', 'approved')->sum('ppn');
        $netPpnPayable = $ppnKeluaranApproved - $ppnMasukanApproved;

        return view('livewire.tax-manager', [
            'taxes' => $taxes,
            'invoices' => $invoices,
            'withholding' => $withholding,
            'filings' => $filings,
            'ppnMasukanApproved' => $ppnMasukanApproved,
            'ppnKeluaranApproved' => $ppnKeluaranApproved,
            'netPpnPayable' => $netPpnPayable,
        ])->layout('layouts.app');
    }
}
