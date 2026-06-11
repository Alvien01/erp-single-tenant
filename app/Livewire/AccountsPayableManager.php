<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentDisbursement;
use App\Models\PaymentSchedule;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsPayableManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'payables'; // payables, disbursements, schedules, aging

    public $isOpen = false;
    public $modalType = ''; // record_disbursement, add_schedule

    // Disbursement fields
    public $disbursement_number;
    public $supplier_id;
    public $purchase_id;
    public $payment_date;
    public $amount = 0;
    public $payment_method = 'Transfer';
    public $notes;
    public $debit_account_id;  // E.g. Hutang Usaha (Accounts Payable)
    public $credit_account_id; // E.g. Bank BCA

    // Schedule fields
    public $schedule_supplier_id;
    public $schedule_purchase_id;
    public $schedule_due_date;
    public $schedule_amount = 0;

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
        $this->schedule_due_date = now()->addDays(30)->format('Y-m-d');
    }

    public function updatedSupplierId($val)
    {
        $this->purchase_id = null;
    }

    public function updatedScheduleSupplierId($val)
    {
        $this->schedule_purchase_id = null;
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;

        if ($type === 'record_disbursement') {
            $this->disbursement_number = 'DIS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Try to guess default accounts
            $this->debit_account_id = Account::where('name', 'like', '%payable%')
                ->orWhere('name', 'like', '%hutang%')
                ->first()?->id;

            $this->credit_account_id = Account::where('name', 'like', '%bank%')
                ->orWhere('name', 'like', '%kas%')
                ->first()?->id;
        }
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->supplier_id = null;
        $this->purchase_id = null;
        $this->amount = 0;
        $this->payment_method = 'Transfer';
        $this->notes = '';

        $this->schedule_supplier_id = null;
        $this->schedule_purchase_id = null;
        $this->schedule_amount = 0;
    }

    public function recordDisbursement()
    {
        $this->resetFields();
        $this->openModal('record_disbursement');
    }

    public function addSchedule()
    {
        $this->resetFields();
        $this->openModal('add_schedule');
    }

    public function saveDisbursement()
    {
        $this->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_id' => 'nullable|exists:purchases,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
        ]);

        $disbursement = PaymentDisbursement::create([
            'disbursement_number' => $this->disbursement_number,
            'supplier_id' => $this->supplier_id,
            'purchase_id' => $this->purchase_id,
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ]);

        // Post Journal Entry
        // Debit Accounts Payable, Credit Cash/Bank
        Journal::create([
            'account_id' => $this->debit_account_id,
            'transaction_date' => $this->payment_date,
            'description' => 'Payment Disbursement ' . $this->disbursement_number . ($this->notes ? ' - ' . $this->notes : ''),
            'reference_number' => $this->disbursement_number,
            'amount' => $this->amount,
            'type' => 'debit',
        ]);

        Journal::create([
            'account_id' => $this->credit_account_id,
            'transaction_date' => $this->payment_date,
            'description' => 'Payment Disbursement ' . $this->disbursement_number . ($this->notes ? ' - ' . $this->notes : ''),
            'reference_number' => $this->disbursement_number,
            'amount' => $this->amount,
            'type' => 'credit',
        ]);

        // Update Account Balances
        $debitAcc = Account::find($this->debit_account_id);
        $debitAcc->decrement('balance', $this->amount); // Debiting liability decreases balance

        $creditAcc = Account::find($this->credit_account_id);
        $creditAcc->decrement('balance', $this->amount); // Crediting asset decreases balance

        // Update Purchase Invoice status if fully paid
        if ($this->purchase_id) {
            $purchase = Purchase::find($this->purchase_id);
            $totalPaid = PaymentDisbursement::where('purchase_id', $this->purchase_id)->sum('amount');
            if ($totalPaid >= $purchase->grand_total) {
                $purchase->status = 'received'; // or paid/completed
                $purchase->save();
            }

            // Mark matching schedule as paid
            PaymentSchedule::where('purchase_id', $this->purchase_id)->update(['status' => 'paid']);
        }

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Accounts Payable',
            'action' => 'Record Payment Disbursement',
            'description' => 'Recorded payment of Rp ' . number_format($this->amount, 2) . ' to Supplier ID ' . $this->supplier_id
        ]);

        session()->flash('success', 'Payment disbursement recorded and journal posted successfully.');
        $this->closeModal();
    }

    public function saveSchedule()
    {
        $this->validate([
            'schedule_supplier_id' => 'required|exists:suppliers,id',
            'schedule_purchase_id' => 'required|exists:purchases,id',
            'schedule_due_date' => 'required|date',
            'schedule_amount' => 'required|numeric|min:1',
        ]);

        PaymentSchedule::create([
            'supplier_id' => $this->schedule_supplier_id,
            'purchase_id' => $this->schedule_purchase_id,
            'due_date' => $this->schedule_due_date,
            'planned_amount' => $this->schedule_amount,
            'status' => 'pending',
        ]);

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Accounts Payable',
            'action' => 'Create Payment Schedule',
            'description' => 'Scheduled payment of Rp ' . number_format($this->schedule_amount, 2) . ' to Supplier ID ' . $this->schedule_supplier_id
        ]);

        session()->flash('success', 'Payment schedule created successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $s = '%' . $this->search . '%';

        // Fetch open purchase invoices
        $purchasesQuery = Purchase::with('supplier')
            ->whereIn('status', ['draft', 'ordered', 'received'])
            ->where(function($q) use ($s) {
                $q->where('purchase_number', 'like', $s)
                  ->orWhereHas('supplier', function($sup) use ($s) {
                      $sup->where('name', 'like', $s);
                  });
            });

        $openPayables = $purchasesQuery->get()->map(function($purchase) {
            $paid = PaymentDisbursement::where('purchase_id', $purchase->id)->sum('amount');
            $purchase->paid_amount = $paid;
            $purchase->remaining_balance = $purchase->grand_total - $paid;
            return $purchase;
        })->filter(function($purchase) {
            return $purchase->remaining_balance > 0;
        });

        // Disbursements
        $disbursements = PaymentDisbursement::with(['supplier', 'purchase'])
            ->where('disbursement_number', 'like', $s)
            ->orWhereHas('supplier', function($sup) use ($s) {
                $sup->where('name', 'like', $s);
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        // Schedules
        $schedules = PaymentSchedule::with(['supplier', 'purchase'])
            ->orderBy('due_date', 'asc')
            ->paginate(10);

        // Suppliers & Purchases for modal dropdowns
        $suppliers = Supplier::all();
        $supplierPurchases = [];
        if ($this->supplier_id) {
            $supplierPurchases = Purchase::where('supplier_id', $this->supplier_id)
                ->get()
                ->filter(function($purchase) {
                    $paid = PaymentDisbursement::where('purchase_id', $purchase->id)->sum('amount');
                    return ($purchase->grand_total - $paid) > 0;
                });
        }

        $scheduleSupplierPurchases = [];
        if ($this->schedule_supplier_id) {
            $scheduleSupplierPurchases = Purchase::where('supplier_id', $this->schedule_supplier_id)
                ->get()
                ->filter(function($purchase) {
                    $paid = PaymentDisbursement::where('purchase_id', $purchase->id)->sum('amount');
                    return ($purchase->grand_total - $paid) > 0;
                });
        }

        // Aging analysis (grouped by supplier)
        $agingData = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->select(
                'suppliers.name as supplier_name',
                'purchases.id as purchase_id',
                'purchases.purchase_number',
                'purchases.purchase_date',
                'purchases.grand_total',
                'purchases.due_date'
            )
            ->whereIn('purchases.status', ['draft', 'ordered', 'received'])
            ->get()
            ->map(function($row) {
                $paid = PaymentDisbursement::where('purchase_id', $row->purchase_id)->sum('amount');
                $row->remaining = $row->grand_total - $paid;

                $dueDate = \Carbon\Carbon::parse($row->due_date ?: $row->purchase_date);
                $diff = $dueDate->diffInDays(now(), false);

                $row->days_overdue = $diff > 0 ? $diff : 0;
                return $row;
            })
            ->filter(fn($row) => $row->remaining > 0);

        $agingSummary = [];
        foreach ($agingData as $item) {
            if (!isset($agingSummary[$item->supplier_name])) {
                $agingSummary[$item->supplier_name] = [
                    'current' => 0,
                    '1_30' => 0,
                    '31_60' => 0,
                    '61_90' => 0,
                    'over_90' => 0,
                    'total' => 0
                ];
            }

            $agingSummary[$item->supplier_name]['total'] += $item->remaining;
            if ($item->days_overdue <= 0) {
                $agingSummary[$item->supplier_name]['current'] += $item->remaining;
            } elseif ($item->days_overdue <= 30) {
                $agingSummary[$item->supplier_name]['1_30'] += $item->remaining;
            } elseif ($item->days_overdue <= 60) {
                $agingSummary[$item->supplier_name]['31_60'] += $item->remaining;
            } elseif ($item->days_overdue <= 90) {
                $agingSummary[$item->supplier_name]['61_90'] += $item->remaining;
            } else {
                $agingSummary[$item->supplier_name]['over_90'] += $item->remaining;
            }
        }

        return view('livewire.accounts-payable-manager', [
            'openPayables' => $openPayables,
            'disbursements' => $disbursements,
            'schedules' => $schedules,
            'suppliers' => $suppliers,
            'supplierPurchases' => $supplierPurchases,
            'scheduleSupplierPurchases' => $scheduleSupplierPurchases,
            'agingSummary' => $agingSummary,
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
