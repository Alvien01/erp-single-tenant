<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentReceipt;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountsReceivableManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'receivables'; // receivables, receipts, aging

    public $isOpen = false;
    public $modalType = ''; // record_receipt

    // Receipt recording fields
    public $receipt_number;
    public $customer_id;
    public $sale_id;
    public $payment_date;
    public $amount = 0;
    public $payment_method = 'Transfer'; // Cash, Transfer, Cheque
    public $notes;
    public $debit_account_id;  // E.g. Bank BCA
    public $credit_account_id; // E.g. Piutang Usaha

    public function mount()
    {
        $this->payment_date = now()->format('Y-m-d');
    }

    public function updatedCustomerId($val)
    {
        // Reset selected sale when customer changes
        $this->sale_id = null;
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
        $this->receipt_number = 'RCT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Try to guess default accounts
        $this->debit_account_id = Account::where('name', 'like', '%bank%')
            ->orWhere('name', 'like', '%kas%')
            ->first()?->id;

        $this->credit_account_id = Account::where('name', 'like', '%piutang%')
            ->orWhere('name', 'like', '%receivable%')
            ->first()?->id;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetFields();
    }

    public function resetFields()
    {
        $this->customer_id = null;
        $this->sale_id = null;
        $this->amount = 0;
        $this->payment_method = 'Transfer';
        $this->notes = '';
    }

    public function recordReceipt()
    {
        $this->resetFields();
        $this->openModal('record_receipt');
    }

    public function saveReceipt()
    {
        $this->validate([
            'customer_id' => 'required|exists:customers,id',
            'sale_id' => 'nullable|exists:sales,id',
            'payment_date' => 'required|date',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'debit_account_id' => 'required|exists:accounts,id',
            'credit_account_id' => 'required|exists:accounts,id',
        ]);

        $receipt = PaymentReceipt::create([
            'receipt_number' => $this->receipt_number,
            'customer_id' => $this->customer_id,
            'sale_id' => $this->sale_id,
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'notes' => $this->notes,
        ]);

        // Post Journal Entry
        // Debit Cash/Bank, Credit Accounts Receivable
        Journal::create([
            'account_id' => $this->debit_account_id,
            'transaction_date' => $this->payment_date,
            'description' => 'Payment Receipt ' . $this->receipt_number . ($this->notes ? ' - ' . $this->notes : ''),
            'reference_number' => $this->receipt_number,
            'amount' => $this->amount,
            'type' => 'debit',
        ]);

        Journal::create([
            'account_id' => $this->credit_account_id,
            'transaction_date' => $this->payment_date,
            'description' => 'Payment Receipt ' . $this->receipt_number . ($this->notes ? ' - ' . $this->notes : ''),
            'reference_number' => $this->receipt_number,
            'amount' => $this->amount,
            'type' => 'credit',
        ]);

        // Update Account Balances
        $debitAcc = Account::find($this->debit_account_id);
        $debitAcc->increment('balance', $this->amount);

        $creditAcc = Account::find($this->credit_account_id);
        $creditAcc->decrement('balance', $this->amount);

        // Update customer credit used
        $customer = Customer::find($this->customer_id);
        $customer->decrement('credit_used', $this->amount);

        // Update Sale Invoice status if fully paid
        if ($this->sale_id) {
            $sale = Sale::find($this->sale_id);
            $totalPaid = PaymentReceipt::where('sale_id', $this->sale_id)->sum('amount');
            if ($totalPaid >= $sale->grand_total) {
                $sale->status = 'delivered'; // or paid/completed
                $sale->save();
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Accounts Receivable',
            'action' => 'Record Payment Receipt',
            'description' => 'Recorded payment of Rp ' . number_format($this->amount, 2) . ' from Customer ID ' . $this->customer_id
        ]);

        session()->flash('success', 'Payment receipt recorded and journal posted successfully.');
        $this->closeModal();
    }

    public function render()
    {
        $s = '%' . $this->search . '%';

        // Fetch open invoices (sales with unpaid balances)
        $salesQuery = Sale::with('customer')
            ->whereIn('status', ['confirmed', 'shipped', 'delivered'])
            ->where(function($q) use ($s) {
                $q->where('invoice_number', 'like', $s)
                  ->orWhereHas('customer', function($c) use ($s) {
                      $c->where('name', 'like', $s);
                  });
            });

        $openInvoices = $salesQuery->get()->map(function($sale) {
            $paid = PaymentReceipt::where('sale_id', $sale->id)->sum('amount');
            $sale->paid_amount = $paid;
            $sale->remaining_balance = $sale->grand_total - $paid;
            return $sale;
        })->filter(function($sale) {
            return $sale->remaining_balance > 0;
        });

        // Receipts
        $receipts = PaymentReceipt::with(['customer', 'sale'])
            ->where('receipt_number', 'like', $s)
            ->orWhereHas('customer', function($c) use ($s) {
                $c->where('name', 'like', $s);
            })
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        // Customer selection for modal
        $customers = Customer::all();
        $customerSales = [];
        if ($this->customer_id) {
            $customerSales = Sale::where('customer_id', $this->customer_id)
                ->whereIn('status', ['confirmed', 'shipped', 'delivered'])
                ->get()
                ->filter(function($sale) {
                    $paid = PaymentReceipt::where('sale_id', $sale->id)->sum('amount');
                    return ($sale->grand_total - $paid) > 0;
                });
        }

        // Aging analysis (grouped by customer)
        $agingData = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'customers.name as customer_name',
                'sales.id as sale_id',
                'sales.invoice_number',
                'sales.sale_date',
                'sales.grand_total',
                'sales.due_date'
            )
            ->whereIn('sales.status', ['confirmed', 'shipped', 'delivered'])
            ->get()
            ->map(function($row) {
                $paid = PaymentReceipt::where('sale_id', $row->sale_id)->sum('amount');
                $row->remaining = $row->grand_total - $paid;
                
                $dueDate = \Carbon\Carbon::parse($row->due_date ?: $row->sale_date);
                $diff = $dueDate->diffInDays(now(), false);
                
                $row->days_overdue = $diff > 0 ? $diff : 0;
                return $row;
            })
            ->filter(fn($row) => $row->remaining > 0);

        $agingSummary = [];
        foreach ($agingData as $item) {
            if (!isset($agingSummary[$item->customer_name])) {
                $agingSummary[$item->customer_name] = [
                    'current' => 0,
                    '1_30' => 0,
                    '31_60' => 0,
                    '61_90' => 0,
                    'over_90' => 0,
                    'total' => 0
                ];
            }

            $agingSummary[$item->customer_name]['total'] += $item->remaining;
            if ($item->days_overdue <= 0) {
                $agingSummary[$item->customer_name]['current'] += $item->remaining;
            } elseif ($item->days_overdue <= 30) {
                $agingSummary[$item->customer_name]['1_30'] += $item->remaining;
            } elseif ($item->days_overdue <= 60) {
                $agingSummary[$item->customer_name]['31_60'] += $item->remaining;
            } elseif ($item->days_overdue <= 90) {
                $agingSummary[$item->customer_name]['61_90'] += $item->remaining;
            } else {
                $agingSummary[$item->customer_name]['over_90'] += $item->remaining;
            }
        }

        return view('livewire.accounts-receivable-manager', [
            'openInvoices' => $openInvoices,
            'receipts' => $receipts,
            'customers' => $customers,
            'customerSales' => $customerSales,
            'agingSummary' => $agingSummary,
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
