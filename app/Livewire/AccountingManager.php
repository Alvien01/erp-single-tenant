<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\ActivityLog;
use App\Models\PeriodClosing;
use App\Models\BankAccount;
use App\Models\CashTransaction;
use App\Models\CashTransfer;
use App\Models\PaymentReceipt;
use App\Models\PaymentDisbursement;
use App\Models\PaymentSchedule;
use App\Models\TaxInvoice;
use App\Models\Budget;
use App\Models\BudgetLine;
use App\Models\Approval;
use Illuminate\Support\Facades\DB;

class AccountingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'dashboard'; 
    public $journal_date;
    public $description;
    public $journal_type = 'general'; 
    public $isOpen = false;
    public $journal_items = []; 
    public $filter_journal_type = '';
    public $selected_account_id;
    public $ledger_start_date;
    public $ledger_end_date;
    public $report_type = 'income_statement'; 
    public $report_period = 'this_month'; 
    public $report_start_date;
    public $report_end_date;
    public $isOpenVoucherModal = false;
    public $selected_voucher_ref = '';
    public $voucher_header = [];
    public $voucher_items = [];
    public $show_void_confirmation = false;
    public $void_reason = '';
    public $closing_date;
    public $closing_note;
    public $accounting_lock_date = null;
    public $new_lock_date = '';
    public $lock_note = '';
    public $isOpenAssetModal = false;
    public $asset_code;
    public $asset_name;
    public $asset_category = 'Equipment';
    public $asset_purchase_date;
    public $asset_purchase_price;
    public $asset_useful_life = 48; 
    public $asset_residual_value = 0;

    public function mount()
    {
        $this->journal_date = now()->format('Y-m-d');
        $this->ledger_start_date = now()->startOfMonth()->format('Y-m-d');
        $this->ledger_end_date = now()->endOfMonth()->format('Y-m-d');
        $this->closing_date = now()->endOfMonth()->format('Y-m-d');

        // Financial reports date presets
        $this->setReportPeriod('this_month');

        // Check active lock date
        $latestClosing = PeriodClosing::where('status', 'closed')->orderBy('closing_date', 'desc')->first();
        if ($latestClosing) {
            $this->accounting_lock_date = $latestClosing->closing_date;
            $this->new_lock_date = $latestClosing->closing_date;
        }

        // Initialize 2 default journal item rows
        $this->resetJournalFields();
    }

    // --- Standard COA Initializer (PSAK Standard) ---
    public function seedStandardCoa()
    {
        $standardAccounts = [
            // Aset Lancar
            ['code' => '1101', 'name' => 'Kas Kecil (Petty Cash)', 'type' => 'asset', 'balance' => 0],
            ['code' => '1102', 'name' => 'Bank BCA - Operasional', 'type' => 'asset', 'balance' => 0],
            ['code' => '1103', 'name' => 'Bank Mandiri', 'type' => 'asset', 'balance' => 0],
            ['code' => '1120', 'name' => 'Piutang Usaha (Accounts Receivable)', 'type' => 'asset', 'balance' => 0],
            ['code' => '1130', 'name' => 'Persediaan Barang Dagang (Inventory)', 'type' => 'asset', 'balance' => 0],
            ['code' => '1140', 'name' => 'Biaya Dibayar Dimuka (Prepaid Expense)', 'type' => 'asset', 'balance' => 0],
            ['code' => '1150', 'name' => 'PPN Masukan (VAT In)', 'type' => 'asset', 'balance' => 0],
            // Aset Tetap
            ['code' => '1201', 'name' => 'Peralatan & Mesin Kantor', 'type' => 'asset', 'balance' => 0],
            ['code' => '1202', 'name' => 'Kendaraan Operasional', 'type' => 'asset', 'balance' => 0],
            ['code' => '1203', 'name' => 'Gedung & Bangunan', 'type' => 'asset', 'balance' => 0],
            ['code' => '1299', 'name' => 'Akumulasi Penyusutan Aset', 'type' => 'asset', 'balance' => 0],
            // Liabilitas Lancar
            ['code' => '2101', 'name' => 'Utang Usaha (Accounts Payable)', 'type' => 'liability', 'balance' => 0],
            ['code' => '2102', 'name' => 'Utang Gaji & Upah', 'type' => 'liability', 'balance' => 0],
            ['code' => '2103', 'name' => 'Utang Pajak (PPh & PPN)', 'type' => 'liability', 'balance' => 0],
            ['code' => '2104', 'name' => 'PPN Keluaran (VAT Out)', 'type' => 'liability', 'balance' => 0],
            // Liabilitas Jk Panjang
            ['code' => '2201', 'name' => 'Utang Bank Jangka Panjang', 'type' => 'liability', 'balance' => 0],
            // Ekuitas
            ['code' => '3101', 'name' => 'Modal Saham / Pemilik', 'type' => 'equity', 'balance' => 0],
            ['code' => '3200', 'name' => 'Laba Ditahan (Retained Earnings)', 'type' => 'equity', 'balance' => 0],
            ['code' => '3999', 'name' => 'Ikhtisar Laba Rugi (Income Summary)', 'type' => 'equity', 'balance' => 0],
            // Pendapatan
            ['code' => '4101', 'name' => 'Pendapatan Penjualan Produk', 'type' => 'income', 'balance' => 0],
            ['code' => '4102', 'name' => 'Pendapatan Jasa / Layanan', 'type' => 'income', 'balance' => 0],
            ['code' => '4901', 'name' => 'Pendapatan Bunga & Lain-lain', 'type' => 'income', 'balance' => 0],
            // Beban Pokok & Operasional
            ['code' => '5101', 'name' => 'Harga Pokok Penjualan (HPP)', 'type' => 'expense', 'balance' => 0],
            ['code' => '5201', 'name' => 'Beban Gaji & Tunjangan Karyawan', 'type' => 'expense', 'balance' => 0],
            ['code' => '5202', 'name' => 'Beban Sewa & Tempat Usaha', 'type' => 'expense', 'balance' => 0],
            ['code' => '5203', 'name' => 'Beban Utilitas, Listrik & Air', 'type' => 'expense', 'balance' => 0],
            ['code' => '5204', 'name' => 'Beban Pemasaran & Promosi', 'type' => 'expense', 'balance' => 0],
            ['code' => '5299', 'name' => 'Beban Penyusutan Aset Tetap', 'type' => 'expense', 'balance' => 0],
            ['code' => '5901', 'name' => 'Beban Bunga & Administrasi Bank', 'type' => 'expense', 'balance' => 0],
        ];

        $inserted = 0;
        foreach ($standardAccounts as $acc) {
            $exists = Account::where('code', $acc['code'])->exists();
            if (!$exists) {
                Account::create($acc);
                $inserted++;
            }
        }

        session()->flash('success', "Berhasil menginisialisasi {$inserted} akun standar PSAK.");
    }

    // --- Multi-Line Journal Entry Methods ---
    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetJournalFields();
    }

    public function resetJournalFields()
    {
        $this->journal_date = now()->format('Y-m-d');
        $this->description = '';
        $this->journal_type = 'general';
        $this->journal_items = [
            ['account_id' => '', 'memo' => '', 'debit' => '', 'credit' => ''],
            ['account_id' => '', 'memo' => '', 'debit' => '', 'credit' => ''],
        ];
    }

    public function createJournal()
    {
        $this->resetJournalFields();
        $this->openModal();
    }

    public function addJournalRow()
    {
        $this->journal_items[] = ['account_id' => '', 'memo' => '', 'debit' => '', 'credit' => ''];
    }

    public function removeJournalRow($index)
    {
        if (count($this->journal_items) > 2) {
            unset($this->journal_items[$index]);
            $this->journal_items = array_values($this->journal_items);
        } else {
            session()->flash('error', 'Minimal harus ada 2 baris transaksi untuk jurnal akuntansi berpasangan.');
        }
    }

    public function getTotalDebitProperty()
    {
        $sum = 0;
        foreach ($this->journal_items as $item) {
            $sum += (float) ($item['debit'] ?? 0);
        }
        return $sum;
    }

    public function getTotalCreditProperty()
    {
        $sum = 0;
        foreach ($this->journal_items as $item) {
            $sum += (float) ($item['credit'] ?? 0);
        }
        return $sum;
    }

    public function getBalanceDiffProperty()
    {
        return abs($this->totalDebit - $this->totalCredit);
    }

    public function getIsBalancedProperty()
    {
        return $this->totalDebit > 0 && $this->balanceDiff < 0.01;
    }

    public function storeJournal()
    {
        // 1. Check Accounting Lock Date
        if ($this->accounting_lock_date && $this->journal_date <= $this->accounting_lock_date) {
            $this->addError('journal_date', 'Periode akuntansi pada tanggal ini telah dikunci (Lock Date: ' . date('d/m/Y', strtotime($this->accounting_lock_date)) . '). Tidak dapat memposting jurnal baru.');
            return;
        }

        // 2. Base Validation
        $this->validate([
            'journal_date' => 'required|date',
            'description' => 'required|string|min:3',
            'journal_type' => 'required|in:general,adjustment,closing',
            'journal_items' => 'required|array|min:2',
            'journal_items.*.account_id' => 'required|exists:accounts,id',
        ]);

        // 3. Balance Check
        $totalDebit = $this->totalDebit;
        $totalCredit = $this->totalCredit;

        if ($totalDebit <= 0) {
            $this->addError('journal_balance', 'Total nominal jurnal tidak boleh nol.');
            return;
        }

        if (abs($totalDebit - $totalCredit) > 0.01) {
            $this->addError('journal_balance', 'Jurnal tidak seimbang (Out of Balance). Selisih: Rp ' . number_format(abs($totalDebit - $totalCredit), 0, ',', '.'));
            return;
        }

        // 4. Generate Unique Sequential Voucher Number
        $todayPrefix = 'JV-' . date('Ymd', strtotime($this->journal_date)) . '-';
        $lastJournal = Journal::where('reference_number', 'like', $todayPrefix . '%')
            ->orderBy('reference_number', 'desc')
            ->first();

        if ($lastJournal) {
            $parts = explode('-', $lastJournal->reference_number);
            $lastSeq = intval(end($parts));
            $seq = $lastSeq + 1;
        } else {
            $seq = 1;
        }

        $ref = $todayPrefix . sprintf('%04d', $seq);

        // 5. DB Transaction
        DB::transaction(function () use ($ref) {
            foreach ($this->journal_items as $item) {
                $debit = (float) ($item['debit'] ?? 0);
                $credit = (float) ($item['credit'] ?? 0);
                $account = Account::find($item['account_id']);
                $itemMemo = !empty($item['memo']) ? $item['memo'] : $this->description;

                if ($debit > 0) {
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->journal_date,
                        'description' => $itemMemo,
                        'reference_number' => $ref,
                        'amount' => $debit,
                        'type' => 'debit',
                        'journal_type' => $this->journal_type,
                        'reference_type' => 'manual',
                    ]);

                    // Adjust Account Balance
                    if (in_array($account->type, ['asset', 'expense'])) {
                        $account->increment('balance', $debit);
                    } else {
                        $account->decrement('balance', $debit);
                    }
                }

                if ($credit > 0) {
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->journal_date,
                        'description' => $itemMemo,
                        'reference_number' => $ref,
                        'amount' => $credit,
                        'type' => 'credit',
                        'journal_type' => $this->journal_type,
                        'reference_type' => 'manual',
                    ]);

                    // Adjust Account Balance
                    if (in_array($account->type, ['liability', 'equity', 'income'])) {
                        $account->increment('balance', $credit);
                    } else {
                        $account->decrement('balance', $credit);
                    }
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Create Journal Entry',
                'description' => "Posted Compound Journal {$ref} ({$this->journal_type}) with total Rp " . number_format($this->totalDebit, 0, ',', '.')
            ]);
        });

        session()->flash('success', "Bukti Jurnal {$ref} berhasil diposting ke Buku Besar secara seimbang.");
        $this->closeModal();
    }

    public function viewVoucher($reference_number)
    {
        $this->selected_voucher_ref = $reference_number;
        $items = Journal::with('account')->where('reference_number', $reference_number)->get();

        if ($items->isEmpty()) {
            session()->flash('error', 'Bukti jurnal tidak ditemukan.');
            return;
        }

        $first = $items->first();
        $this->voucher_header = [
            'reference_number' => $first->reference_number,
            'transaction_date' => $first->transaction_date,
            'journal_type' => $first->journal_type,
            'description' => $first->description,
            'total_debit' => $items->where('type', 'debit')->sum('amount'),
            'total_credit' => $items->where('type', 'credit')->sum('amount'),
            'is_reversing' => str_starts_with($first->reference_number, 'REV-'),
        ];

        $this->voucher_items = $items;
        $this->show_void_confirmation = false;
        $this->void_reason = '';
        $this->isOpenVoucherModal = true;
    }

    public function closeVoucherModal()
    {
        $this->isOpenVoucherModal = false;
        $this->selected_voucher_ref = '';
        $this->voucher_items = [];
        $this->show_void_confirmation = false;
        $this->void_reason = '';
    }

    public function confirmVoidVoucher()
    {
        $this->show_void_confirmation = true;
    }

    public function cancelVoidVoucher()
    {
        $this->show_void_confirmation = false;
    }

    public function executeVoidVoucher()
    {
        $items = Journal::with('account')->where('reference_number', $this->selected_voucher_ref)->get();
        if ($items->isEmpty()) {
            session()->flash('error', 'Bukti jurnal tidak ditemukan.');
            return;
        }

        $first = $items->first();

        // Check lock date
        if ($this->accounting_lock_date && $first->transaction_date <= $this->accounting_lock_date) {
            session()->flash('error', 'Tidak dapat membatalkan transaksi pada periode yang telah dikunci auditor.');
            return;
        }

        // Check if already reversed
        $revRef = 'REV-' . $first->reference_number;
        $alreadyReversed = Journal::where('reference_number', $revRef)->exists();
        if ($alreadyReversed || str_starts_with($first->reference_number, 'REV-')) {
            session()->flash('error', 'Jurnal ini sudah pernah dibatalkan (dibalik).');
            return;
        }

        $today = now()->format('Y-m-d');
        $reason = $this->void_reason ?: 'Pembatalan transaksi oleh user';

        DB::transaction(function () use ($items, $revRef, $today, $reason, $first) {
            foreach ($items as $item) {
                // Invert type: debit becomes credit, credit becomes debit
                $newType = ($item->type === 'debit') ? 'credit' : 'debit';
                $account = $item->account;

                Journal::create([
                    'account_id' => $item->account_id,
                    'transaction_date' => $today,
                    'description' => "[VOID {$first->reference_number}] {$reason} - " . $item->description,
                    'reference_number' => $revRef,
                    'amount' => $item->amount,
                    'type' => $newType,
                    'journal_type' => 'adjustment',
                    'reference_type' => 'reversing_entry',
                    'reference_id' => $item->id,
                ]);

                // Revert Account Balance
                if ($newType === 'debit') {
                    if (in_array($account->type, ['asset', 'expense'])) {
                        $account->increment('balance', $item->amount);
                    } else {
                        $account->decrement('balance', $item->amount);
                    }
                } else {
                    if (in_array($account->type, ['liability', 'equity', 'income'])) {
                        $account->increment('balance', $item->amount);
                    } else {
                        $account->decrement('balance', $item->amount);
                    }
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Void Journal Voucher',
                'description' => "Voided {$first->reference_number} with reversing voucher {$revRef}. Reason: {$reason}"
            ]);
        });

        session()->flash('success', "Bukti jurnal berhasil dibatalkan dengan Jurnal Pembalik ({$revRef}). Saldo akun telah dipulihkan.");
        $this->closeVoucherModal();
    }

    // --- Financial Reports Engine ---
    public function setReportPeriod($preset)
    {
        $this->report_period = $preset;
        if ($preset === 'this_month') {
            $this->report_start_date = now()->startOfMonth()->format('Y-m-d');
            $this->report_end_date = now()->endOfMonth()->format('Y-m-d');
        } elseif ($preset === 'last_month') {
            $this->report_start_date = now()->subMonth()->startOfMonth()->format('Y-m-d');
            $this->report_end_date = now()->subMonth()->endOfMonth()->format('Y-m-d');
        } elseif ($preset === 'this_quarter') {
            $this->report_start_date = now()->startOfQuarter()->format('Y-m-d');
            $this->report_end_date = now()->endOfQuarter()->format('Y-m-d');
        } elseif ($preset === 'this_year') {
            $this->report_start_date = now()->startOfYear()->format('Y-m-d');
            $this->report_end_date = now()->endOfYear()->format('Y-m-d');
        } elseif ($preset === 'all_time') {
            $this->report_start_date = '2020-01-01';
            $this->report_end_date = now()->format('Y-m-d');
        }
    }

    public function updatedReportPeriod($value)
    {
        if ($value !== 'custom') {
            $this->setReportPeriod($value);
        }
    }

    public function drillDownToLedger($accountId)
    {
        $this->selected_account_id = $accountId;
        $this->ledger_start_date = $this->report_start_date;
        $this->ledger_end_date = $this->report_end_date;
        $this->activeTab = 'ledger_detail';
    }

    public function getFinancialReportData()
    {
        $start = $this->report_start_date;
        $end = $this->report_end_date;

        if ($this->report_type === 'income_statement') {
            $journalTotals = Journal::selectRaw('account_id, type, SUM(amount) as total')
                ->whereBetween('transaction_date', [$start, $end])
                ->groupBy('account_id', 'type')
                ->get()
                ->groupBy('account_id');

            // Income accounts
            $incomeAccounts = Account::where('type', 'income')->orderBy('code')->get();
            $operatingRevenue = [];
            $otherRevenue = [];
            $totalRevenue = 0;

            foreach ($incomeAccounts as $acc) {
                $accTotals = $journalTotals->get($acc->id);
                $credits = (float)($accTotals?->firstWhere('type', 'credit')?->total ?? 0);
                $debits  = (float)($accTotals?->firstWhere('type', 'debit')?->total ?? 0);
                $net = $credits - $debits;
                if ($net != 0 || $acc->balance != 0) {
                    $row = ['id' => $acc->id, 'code' => $acc->code, 'name' => $acc->name, 'amount' => $net];
                    if (str_starts_with($acc->code, '49') || str_contains(strtolower($acc->name), 'lain')) {
                        $otherRevenue[] = $row;
                    } else {
                        $operatingRevenue[] = $row;
                    }
                    $totalRevenue += $net;
                }
            }

            // Expense accounts
            $expenseAccounts = Account::where('type', 'expense')->orderBy('code')->get();
            $cogsList = [];
            $operatingExpenses = [];
            $otherExpenses = [];
            $totalCogs = 0;
            $totalOperatingExpense = 0;
            $totalOtherExpense = 0;

            foreach ($expenseAccounts as $acc) {
                $accTotals = $journalTotals->get($acc->id);
                $debits  = (float)($accTotals?->firstWhere('type', 'debit')?->total ?? 0);
                $credits = (float)($accTotals?->firstWhere('type', 'credit')?->total ?? 0);
                $net = $debits - $credits;
                if ($net != 0 || $acc->balance != 0) {
                    $row = ['id' => $acc->id, 'code' => $acc->code, 'name' => $acc->name, 'amount' => $net];
                    if (str_starts_with($acc->code, '51') || str_contains(strtolower($acc->name), 'hpp') || str_contains(strtolower($acc->name), 'pokok')) {
                        $cogsList[] = $row;
                        $totalCogs += $net;
                    } elseif (str_starts_with($acc->code, '59') || str_contains(strtolower($acc->name), 'lain')) {
                        $otherExpenses[] = $row;
                        $totalOtherExpense += $net;
                    } else {
                        $operatingExpenses[] = $row;
                        $totalOperatingExpense += $net;
                    }
                }
            }

            $grossProfit = $totalRevenue - $totalCogs;
            $operatingIncome = $grossProfit - $totalOperatingExpense;
            $sumOtherRev = array_sum(array_column($otherRevenue, 'amount'));
            $netProfit = $operatingIncome + $sumOtherRev - $totalOtherExpense;

            return [
                'operatingRevenue' => $operatingRevenue,
                'otherRevenue' => $otherRevenue,
                'totalRevenue' => $totalRevenue,
                'cogsList' => $cogsList,
                'totalCogs' => $totalCogs,
                'grossProfit' => $grossProfit,
                'operatingExpenses' => $operatingExpenses,
                'totalOperatingExpense' => $totalOperatingExpense,
                'operatingIncome' => $operatingIncome,
                'otherExpenses' => $otherExpenses,
                'totalOtherExpense' => $totalOtherExpense,
                'netProfit' => $netProfit,
            ];

        } elseif ($this->report_type === 'balance_sheet') {
            // As of report_end_date
            $accounts = Account::all();

            $journalTotals = Journal::selectRaw('account_id, type, SUM(amount) as total')
                ->where('transaction_date', '<=', $end)
                ->groupBy('account_id', 'type')
                ->get()
                ->groupBy('account_id');

            $currentAssets = [];
            $nonCurrentAssets = [];
            $currentLiabilities = [];
            $longTermLiabilities = [];
            $equityList = [];

            $totalCurrentAssets = 0;
            $totalNonCurrentAssets = 0;
            $totalCurrentLiabilities = 0;
            $totalLongTermLiabilities = 0;
            $totalEquity = 0;

            foreach ($accounts as $acc) {
                $accTotals = $journalTotals->get($acc->id);
                $debits  = (float)($accTotals?->firstWhere('type', 'debit')?->total ?? 0);
                $credits = (float)($accTotals?->firstWhere('type', 'credit')?->total ?? 0);

                if ($acc->type === 'asset') {
                    $bal = $debits - $credits;
                    $row = ['id' => $acc->id, 'code' => $acc->code, 'name' => $acc->name, 'amount' => $bal];
                    if (str_starts_with($acc->code, '12')) {
                        $nonCurrentAssets[] = $row;
                        $totalNonCurrentAssets += $bal;
                    } else {
                        $currentAssets[] = $row;
                        $totalCurrentAssets += $bal;
                    }
                } elseif ($acc->type === 'liability') {
                    $bal = $credits - $debits;
                    $row = ['id' => $acc->id, 'code' => $acc->code, 'name' => $acc->name, 'amount' => $bal];
                    if (str_starts_with($acc->code, '22')) {
                        $longTermLiabilities[] = $row;
                        $totalLongTermLiabilities += $bal;
                    } else {
                        $currentLiabilities[] = $row;
                        $totalCurrentLiabilities += $bal;
                    }
                } elseif ($acc->type === 'equity') {
                    $bal = $credits - $debits;
                    $row = ['id' => $acc->id, 'code' => $acc->code, 'name' => $acc->name, 'amount' => $bal];
                    $equityList[] = $row;
                    $totalEquity += $bal;
                }
            }

            // Current year Net Profit to balance the equity
            $totalInc = Journal::whereHas('account', fn($q) => $q->where('type', 'income'))->where('type', 'credit')->where('transaction_date', '<=', $end)->sum('amount')
                      - Journal::whereHas('account', fn($q) => $q->where('type', 'income'))->where('type', 'debit')->where('transaction_date', '<=', $end)->sum('amount');
            $totalExp = Journal::whereHas('account', fn($q) => $q->where('type', 'expense'))->where('type', 'debit')->where('transaction_date', '<=', $end)->sum('amount')
                      - Journal::whereHas('account', fn($q) => $q->where('type', 'expense'))->where('type', 'credit')->where('transaction_date', '<=', $end)->sum('amount');
            $currentPeriodProfit = $totalInc - $totalExp;

            $totalAssets = $totalCurrentAssets + $totalNonCurrentAssets;
            $totalLiabilitiesAndEquity = $totalCurrentLiabilities + $totalLongTermLiabilities + $totalEquity + $currentPeriodProfit;

            return [
                'currentAssets' => $currentAssets,
                'totalCurrentAssets' => $totalCurrentAssets,
                'nonCurrentAssets' => $nonCurrentAssets,
                'totalNonCurrentAssets' => $totalNonCurrentAssets,
                'totalAssets' => $totalAssets,
                'currentLiabilities' => $currentLiabilities,
                'totalCurrentLiabilities' => $totalCurrentLiabilities,
                'longTermLiabilities' => $longTermLiabilities,
                'totalLongTermLiabilities' => $totalLongTermLiabilities,
                'equityList' => $equityList,
                'currentPeriodProfit' => $currentPeriodProfit,
                'totalEquity' => $totalEquity + $currentPeriodProfit,
                'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
                'isBalanced' => abs($totalAssets - $totalLiabilitiesAndEquity) < 1,
            ];

        } elseif ($this->report_type === 'trial_balance') {
            $accounts = Account::orderBy('code')->get();
            $trialRows = [];
            $totalDebitMut = 0;
            $totalCreditMut = 0;
            $totalEndingDebit = 0;
            $totalEndingCredit = 0;

            $stats = Journal::selectRaw("
                account_id,
                SUM(CASE WHEN transaction_date < ? AND type = 'debit' THEN amount ELSE 0 END) as deb_before,
                SUM(CASE WHEN transaction_date < ? AND type = 'credit' THEN amount ELSE 0 END) as cred_before,
                SUM(CASE WHEN transaction_date >= ? AND transaction_date <= ? AND type = 'debit' THEN amount ELSE 0 END) as deb_mut,
                SUM(CASE WHEN transaction_date >= ? AND transaction_date <= ? AND type = 'credit' THEN amount ELSE 0 END) as cred_mut
            ", [$start, $start, $start, $end, $start, $end])
            ->where('transaction_date', '<=', $end)
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

            foreach ($accounts as $acc) {
                $row = $stats->get($acc->id);
                $debBefore  = (float)($row?->deb_before ?? 0);
                $credBefore = (float)($row?->cred_before ?? 0);
                
                $opening = in_array($acc->type, ['asset', 'expense']) ? ($debBefore - $credBefore) : ($credBefore - $debBefore);

                $debMut  = (float)($row?->deb_mut ?? 0);
                $credMut = (float)($row?->cred_mut ?? 0);

                // Ending balance
                $ending = in_array($acc->type, ['asset', 'expense']) ? ($opening + $debMut - $credMut) : ($opening + $credMut - $debMut);

                $totalDebitMut += $debMut;
                $totalCreditMut += $credMut;

                if (in_array($acc->type, ['asset', 'expense'])) {
                    $totalEndingDebit += max(0, $ending);
                    $totalEndingCredit += max(0, -$ending);
                } else {
                    $totalEndingCredit += max(0, $ending);
                    $totalEndingDebit += max(0, -$ending);
                }

                $trialRows[] = [
                    'id' => $acc->id,
                    'code' => $acc->code,
                    'name' => $acc->name,
                    'type' => $acc->type,
                    'opening' => $opening,
                    'debit' => $debMut,
                    'credit' => $credMut,
                    'ending' => $ending,
                ];
            }

            return [
                'rows' => $trialRows,
                'totalDebitMut' => $totalDebitMut,
                'totalCreditMut' => $totalCreditMut,
                'totalEndingDebit' => $totalEndingDebit,
                'totalEndingCredit' => $totalEndingCredit,
                'isBalanced' => abs($totalDebitMut - $totalCreditMut) < 1,
            ];

        } elseif ($this->report_type === 'cash_flow') {
            // Cash & Bank accounts
            $cashAccounts = Account::where('type', 'asset')->where(function($q) {
                $q->where('code', 'like', '110%')
                  ->orWhere('name', 'like', '%kas%')
                  ->orWhere('name', 'like', '%bank%');
            })->pluck('id');

            $cashIn = Journal::whereIn('account_id', $cashAccounts)
                ->where('type', 'debit')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            $cashOut = Journal::whereIn('account_id', $cashAccounts)
                ->where('type', 'credit')
                ->whereBetween('transaction_date', [$start, $end])
                ->sum('amount');

            // Opening cash before $start
            $openIn = Journal::whereIn('account_id', $cashAccounts)->where('type', 'debit')->where('transaction_date', '<', $start)->sum('amount');
            $openOut = Journal::whereIn('account_id', $cashAccounts)->where('type', 'credit')->where('transaction_date', '<', $start)->sum('amount');
            $openingCash = $openIn - $openOut;
            $netChange = $cashIn - $cashOut;
            $closingCash = $openingCash + $netChange;

            return [
                'openingCash' => $openingCash,
                'cashIn' => $cashIn,
                'cashOut' => $cashOut,
                'netChange' => $netChange,
                'closingCash' => $closingCash,
            ];
        }

        return [];
    }

    // --- Period Closing & Lock Date Management ---
    public function setAccountingLockDate()
    {
        $this->validate([
            'new_lock_date' => 'required|date',
            'lock_note' => 'nullable|string',
        ]);

        $this->accounting_lock_date = $this->new_lock_date;

        PeriodClosing::create([
            'closing_date' => $this->new_lock_date,
            'closed_by' => auth()->id() ?? 1,
            'status' => 'closed',
            'notes' => $this->lock_note ?: 'Accounting Lock Date diaktifkan hingga ' . $this->new_lock_date,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Set Lock Date',
            'description' => 'Accounting lock date ditetapkan hingga: ' . $this->new_lock_date
        ]);

        session()->flash('success', 'Accounting Lock Date berhasil disimpan. Transaksi pada atau sebelum tanggal ini dikunci dari perubahan.');
    }

    public function unlockAccountingDate()
    {
        $this->accounting_lock_date = null;
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Unlock Date',
            'description' => 'Accounting lock date dinonaktifkan oleh administrator.'
        ]);
        session()->flash('info', 'Accounting lock date dinonaktifkan.');
    }

    public function executeClosing()
    {
        $this->validate([
            'closing_date' => 'required|date',
            'closing_note' => 'nullable|string',
        ]);

        // Find or create retained earnings (Laba Ditahan) account
        $retainedEarningsAccount = Account::firstOrCreate(
            ['code' => '3200'],
            ['name' => 'Laba Ditahan', 'type' => 'equity', 'balance' => 0]
        );

        // Find or create Income Summary (Ikhtisar Laba Rugi) account
        $incomeSummaryAccount = Account::firstOrCreate(
            ['code' => '3999'],
            ['name' => 'Ikhtisar Laba Rugi', 'type' => 'equity', 'balance' => 0]
        );

        // Fetch nominal accounts (income & expense) with positive balances
        $nominalAccounts = Account::whereIn('type', ['income', 'expense'])->where('balance', '!=', 0)->get();

        if ($nominalAccounts->isEmpty()) {
            session()->flash('error', 'Tidak ada akun nominal (pendapatan atau beban) dengan saldo berjalan untuk ditutup.');
            return;
        }

        DB::transaction(function () use ($nominalAccounts, $incomeSummaryAccount, $retainedEarningsAccount) {
            $ref = 'CLS-' . date('Ymd', strtotime($this->closing_date)) . '-' . strtoupper(substr(uniqid(), -4));

            foreach ($nominalAccounts as $account) {
                $balance = $account->balance;
                if ($balance == 0) continue;

                if ($account->type === 'income') {
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    $account->balance = 0;
                    $account->save();

                    $incomeSummaryAccount->increment('balance', $balance);
                } elseif ($account->type === 'expense') {
                    Journal::create([
                        'account_id' => $account->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Closing entry for ' . $account->name,
                        'reference_number' => $ref,
                        'amount' => $balance,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    $account->balance = 0;
                    $account->save();

                    $incomeSummaryAccount->decrement('balance', $balance);
                }
            }

            // Transfer net income/loss from Income Summary to Retained Earnings
            $netIncome = $incomeSummaryAccount->balance;
            if ($netIncome != 0) {
                if ($netIncome > 0) {
                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Income to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $netIncome,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $retainedEarningsAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Income to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $netIncome,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    $retainedEarningsAccount->increment('balance', $netIncome);
                } else {
                    $lossAmount = abs($netIncome);
                    Journal::create([
                        'account_id' => $incomeSummaryAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Loss to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $lossAmount,
                        'type' => 'credit',
                        'journal_type' => 'closing',
                    ]);

                    Journal::create([
                        'account_id' => $retainedEarningsAccount->id,
                        'transaction_date' => $this->closing_date,
                        'description' => 'Transfer Net Loss to Retained Earnings',
                        'reference_number' => $ref,
                        'amount' => $lossAmount,
                        'type' => 'debit',
                        'journal_type' => 'closing',
                    ]);

                    $retainedEarningsAccount->decrement('balance', $lossAmount);
                }

                $incomeSummaryAccount->balance = 0;
                $incomeSummaryAccount->save();
            }

            // Save period closing log and auto-set Lock Date
            PeriodClosing::create([
                'closing_date' => $this->closing_date,
                'status' => 'closed',
                'closed_by' => auth()->id() ?? 1,
                'notes' => $this->closing_note ?: 'Period closing successfully compiled.',
            ]);

            $this->accounting_lock_date = $this->closing_date;

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Close Fiscal Period',
                'description' => 'Closed fiscal period up to ' . $this->closing_date
            ]);
        });

        session()->flash('success', 'Tutup buku fiskal berhasil dilaksanakan. Seluruh akun pendapatan & beban telah di-reset ke 0 dan dipindahkan ke Laba Ditahan.');
        $this->activeTab = 'coa';
    }

    // --- Fixed Asset methods ---
    public function createAsset()
    {
        $this->asset_code = 'AST-' . now()->format('Y') . '-' . sprintf('%04d', Asset::count() + 1);
        $this->asset_name = '';
        $this->asset_category = 'Equipment';
        $this->asset_purchase_date = now()->format('Y-m-d');
        $this->asset_purchase_price = 0;
        $this->asset_useful_life = 48;
        $this->asset_residual_value = 0;
        $this->isOpenAssetModal = true;
    }

    public function storeAsset()
    {
        $this->validate([
            'asset_code' => 'required|string|unique:assets,asset_code',
            'asset_name' => 'required|string|max:255',
            'asset_category' => 'required|string',
            'asset_purchase_date' => 'required|date',
            'asset_purchase_price' => 'required|numeric|min:100000',
            'asset_useful_life' => 'required|integer|min:1',
            'asset_residual_value' => 'required|numeric|min:0|max:' . ($this->asset_purchase_price ?? 999999999),
        ]);

        Asset::create([
            'asset_code' => $this->asset_code,
            'asset_name' => $this->asset_name,
            'category' => $this->asset_category,
            'purchase_date' => $this->asset_purchase_date,
            'purchase_price' => $this->asset_purchase_price,
            'useful_life_months' => $this->asset_useful_life,
            'residual_value' => $this->asset_residual_value,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Register Asset',
            'description' => 'Fixed Asset registered: ' . $this->asset_name . ' (' . $this->asset_code . ')'
        ]);

        session()->flash('success', 'Asset registered successfully.');
        $this->isOpenAssetModal = false;
    }

    public function runDepreciation()
    {
        $assets = Asset::all();
        $processedCount = 0;

        $depExpenseAcc = Account::firstOrCreate(
            ['code' => '5299'],
            ['name' => 'Beban Penyusutan Aset Tetap', 'type' => 'expense', 'balance' => 0]
        );

        $accumDepAcc = Account::firstOrCreate(
            ['code' => '1299'],
            ['name' => 'Akumulasi Penyusutan Aset', 'type' => 'asset', 'balance' => 0]
        );

        $depStats = AssetDepreciation::selectRaw('asset_id, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('asset_id')
            ->get()
            ->keyBy('asset_id');

        foreach ($assets as $asset) {
            $existingMonths = (int)($depStats[$asset->id]->count ?? 0);

            if ($existingMonths >= $asset->useful_life_months) {
                continue; 
            }

            $monthlyAmount = ($asset->purchase_price - $asset->residual_value) / $asset->useful_life_months;

            $purchaseTime = strtotime($asset->purchase_date);
            $nextMonthTime = strtotime("+" . ($existingMonths + 1) . " months", $purchaseTime);
            $depreciationDate = date('Y-m-d', $nextMonthTime);

            if (strtotime($depreciationDate) > time()) {
                continue;
            }

            $accumulated = (float)($depStats[$asset->id]->total ?? 0) + $monthlyAmount;
            $bookValue = $asset->purchase_price - $accumulated;

            AssetDepreciation::create([
                'asset_id' => $asset->id,
                'depreciation_date' => $depreciationDate,
                'amount' => $monthlyAmount,
                'accumulated' => $accumulated,
                'book_value' => $bookValue,
            ]);

            $ref = 'DEP-' . now()->format('Ymd') . '-' . sprintf('%04d', $asset->id);

            Journal::create([
                'account_id' => $depExpenseAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'debit',
                'journal_type' => 'adjustment',
            ]);

            Journal::create([
                'account_id' => $accumDepAcc->id,
                'transaction_date' => $depreciationDate,
                'description' => "Accum. Depreciation of {$asset->asset_name} - Month " . ($existingMonths + 1),
                'reference_number' => $ref,
                'amount' => $monthlyAmount,
                'type' => 'credit',
                'journal_type' => 'adjustment',
            ]);

            $depExpenseAcc->increment('balance', $monthlyAmount);
            $accumDepAcc->decrement('balance', $monthlyAmount); 

            $processedCount++;
        }

        if ($processedCount > 0) {
            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Accounting',
                'action' => 'Run Depreciation',
                'description' => "Calculated and posted depreciation for {$processedCount} assets."
            ]);
            session()->flash('success', "Depreciation runs successfully executed for {$processedCount} asset(s).");
        } else {
            session()->flash('info', 'No assets require depreciation run at this time.');
        }
    }

    public function getDashboardStats()
    {
        $accounts = Account::all();
        $totalAssets = $accounts->where('type', 'asset')->sum('balance');
        $totalLiabilities = $accounts->where('type', 'liability')->sum('balance');
        $totalEquity = $accounts->where('type', 'equity')->sum('balance');
        $totalIncome = $accounts->where('type', 'income')->sum('balance');
        $totalExpenses = $accounts->where('type', 'expense')->sum('balance');
        $netProfit = $totalIncome - $totalExpenses;

        $cashBankBalance = $accounts->where('type', 'asset')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'kas') || str_contains($nameLower, 'bank') || str_contains($nameLower, 'cash');
        })->sum('balance');

        $receivablesBalance = $accounts->where('type', 'asset')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'piutang') || str_contains($nameLower, 'receivable') || str_contains($nameLower, 'ar');
        })->sum('balance');

        $payablesBalance = $accounts->where('type', 'liability')->filter(function($acc) {
            $nameLower = strtolower($acc->name);
            return str_contains($nameLower, 'hutang') || str_contains($nameLower, 'payable') || str_contains($nameLower, 'ap');
        })->sum('balance');

        // Sub-module stats
        $cashTransactionsCount = \App\Models\CashTransaction::count();
        $cashTransactionsTotal = \App\Models\CashTransaction::sum('amount');
        $cashTransfersCount = \App\Models\CashTransfer::count();
        $bankAccountsCount = \App\Models\BankAccount::count();

        $receiptsCount = \App\Models\PaymentReceipt::count();
        $receiptsTotal = \App\Models\PaymentReceipt::sum('amount');

        $disbursementsCount = \App\Models\PaymentDisbursement::count();
        $disbursementsTotal = \App\Models\PaymentDisbursement::sum('amount');
        $pendingSchedulesCount = \App\Models\PaymentSchedule::where('status', 'pending')->count();
        $pendingSchedulesTotal = \App\Models\PaymentSchedule::where('status', 'pending')->sum('planned_amount');

        $taxInvoicesCount = \App\Models\TaxInvoice::count();
        $taxPPNMasukan = \App\Models\TaxInvoice::where('type', 'masukan')->sum('ppn');
        $taxPPNKeluaran = \App\Models\TaxInvoice::where('type', 'keluaran')->sum('ppn');

        $budgetsCount = \App\Models\Budget::count();
        $totalPlannedBudget = \App\Models\BudgetLine::sum('planned_amount');

        $pendingApprovalsCount = \App\Models\Approval::where('status', 'pending')->count();

        $assetsCount = \App\Models\Asset::count();
        $totalAssetCost = \App\Models\Asset::sum('purchase_price');
        $totalDepreciation = \App\Models\AssetDepreciation::sum('amount');

        $recentTransactions = Journal::with('account')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->take(6)
            ->get();

        return [
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'totalEquity' => $totalEquity,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'cashBankBalance' => $cashBankBalance,
            'receivablesBalance' => $receivablesBalance,
            'payablesBalance' => $payablesBalance,
            'recentTransactions' => $recentTransactions,
        ];
    }

    public function render()
    {
        $coaQuery = Account::query()->orderBy('code');
        if ($this->search) {
            $coaQuery->where('name', 'like', '%' . $this->search . '%')
                     ->orWhere('code', 'like', '%' . $this->search . '%');
        }

        // Journals filter
        $journalsQuery = Journal::with('account');
        if ($this->filter_journal_type) {
            $journalsQuery->where('journal_type', $this->filter_journal_type);
        }
        $journals = $journalsQuery->orderBy('transaction_date', 'desc')->orderBy('reference_number', 'desc')->paginate(15);

        // General Ledger Detail
        $ledgerEntries = [];
        $openingBalance = 0;
        $closingBalance = 0;
        if ($this->selected_account_id) {
            $account = Account::find($this->selected_account_id);
            if ($account) {
                // Calculate opening balance before start date
                $debitsBefore = Journal::where('account_id', $this->selected_account_id)
                    ->where('type', 'debit')
                    ->where('transaction_date', '<', $this->ledger_start_date)
                    ->sum('amount');
                $creditsBefore = Journal::where('account_id', $this->selected_account_id)
                    ->where('type', 'credit')
                    ->where('transaction_date', '<', $this->ledger_start_date)
                    ->sum('amount');

                if (in_array($account->type, ['asset', 'expense'])) {
                    $openingBalance = $debitsBefore - $creditsBefore;
                } else {
                    $openingBalance = $creditsBefore - $debitsBefore;
                }

                // Fetch ledger entries in date range
                $ledgerEntries = Journal::where('account_id', $this->selected_account_id)
                    ->whereBetween('transaction_date', [$this->ledger_start_date, $this->ledger_end_date])
                    ->orderBy('transaction_date')
                    ->orderBy('id')
                    ->get();

                // Compute running balances
                $currentTemp = $openingBalance;
                foreach ($ledgerEntries as $entry) {
                    if ($entry->type === 'debit') {
                        if (in_array($account->type, ['asset', 'expense'])) {
                            $currentTemp += $entry->amount;
                        } else {
                            $currentTemp -= $entry->amount;
                        }
                    } else { // credit
                        if (in_array($account->type, ['asset', 'expense'])) {
                            $currentTemp -= $entry->amount;
                        } else {
                            $currentTemp += $entry->amount;
                        }
                    }
                    $entry->running_balance = $currentTemp;
                }
                $closingBalance = $currentTemp;
            }
        }

        // Financial reports data
        $reportData = [];
        if ($this->activeTab === 'financial_reports') {
            $reportData = $this->getFinancialReportData();
        }

        // Period Closings history
        $closings = PeriodClosing::orderBy('closing_date', 'desc')->paginate(10);

        $accounts = $coaQuery->get();
        $allAccounts = $this->search ? Account::orderBy('code')->get() : $accounts;

        return view('livewire.accounting-manager', [
            'accounts' => $accounts,
            'journals' => $journals,
            'assets' => Asset::with('depreciations')->paginate(10),
            'allAccounts' => $allAccounts,
            'ledgerEntries' => $ledgerEntries,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'closings' => $closings,
            'stats' => $this->activeTab === 'dashboard' ? $this->getDashboardStats() : [],
            'reportData' => $reportData,
        ])->layout('layouts.app');
    }
}
