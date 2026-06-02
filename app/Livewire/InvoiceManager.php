<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Sale;
use App\Models\Purchase;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\ActivityLog;

class InvoiceManager extends Component
{
    use WithPagination;

    public $search = '';

    // Form fields
    public $invoice_id;
    public $invoice_number;
    public $type = 'credit_note'; // credit_note, debit_note
    public $customer_id;
    public $supplier_id;
    public $original_invoice_id; // link to another invoice
    public $date;
    public $due_date;
    public $total_amount = 0;
    public $tax_amount = 0;
    public $grand_total = 0;
    public $status = 'draft';
    public $notes;
    public $items = []; // [{product_id, quantity, unit_price, total_price}]

    public $isOpen = false;
    public $isEditMode = false;

    public function mount()
    {
        $this->date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->invoice_id = null;
        $this->invoice_number = '';
        $this->type = 'credit_note';
        $this->customer_id = '';
        $this->supplier_id = '';
        $this->original_invoice_id = '';
        $this->date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');
        $this->total_amount = 0;
        $this->tax_amount = 0;
        $this->grand_total = 0;
        $this->status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
    }

    public function updatedType()
    {
        $this->customer_id = '';
        $this->supplier_id = '';
        $this->original_invoice_id = '';
        $this->items = [];
        $this->calculateTotals();
    }

    public function updatedOriginalInvoiceId($origId)
    {
        if ($origId) {
            if ($this->type === 'credit_note') {
                $sale = Sale::with('items.product')->find($origId);
                if ($sale) {
                    $this->customer_id = $sale->customer_id;
                    $this->items = [];
                    foreach ($sale->items as $item) {
                        $this->items[] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ];
                    }
                }
            } else {
                $pur = Purchase::with('items.product')->find($origId);
                if ($pur) {
                    $this->supplier_id = $pur->supplier_id;
                    $this->items = [];
                    foreach ($pur->items as $item) {
                        $this->items[] = [
                            'product_id' => $item->product_id,
                            'name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total_price' => $item->total_price,
                        ];
                    }
                }
            }
            $this->calculateTotals();
        }
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'name' => '',
            'quantity' => 1,
            'unit_price' => 0,
            'total_price' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotals();
    }

    public function updatedItems($value, $key)
    {
        // Format of $key: "0.quantity" or "0.unit_price" or "0.product_id"
        $parts = explode('.', $key);
        if (count($parts) === 2) {
            $index = $parts[0];
            $field = $parts[1];

            if ($field === 'product_id') {
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['name'] = $product->name;
                    $this->items[$index]['unit_price'] = $this->type === 'credit_note' ? $product->sale_price : $product->purchase_cost;
                }
            }

            $qty = floatval($this->items[$index]['quantity'] ?? 0);
            $price = floatval($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['total_price'] = $qty * $price;
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->total_amount = 0;
        foreach ($this->items as $item) {
            $this->total_amount += floatval($item['total_price'] ?? 0);
        }
        $this->tax_amount = $this->total_amount * 0.11; // 11% VAT standard
        $this->grand_total = $this->total_amount + $this->tax_amount;
    }

    public function create()
    {
        $this->resetInputFields();
        $prefix = $this->type === 'credit_note' ? 'CN-' : 'DN-';
        $this->invoice_number = $prefix . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
        $this->openModal();
    }

    public function store()
    {
        $this->validate([
            'invoice_number' => 'required|string|unique:invoices,invoice_number,' . $this->invoice_id,
            'type' => 'required|in:credit_note,debit_note',
            'customer_id' => 'required_if:type,credit_note',
            'supplier_id' => 'required_if:type,debit_note',
            'date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:date',
            'status' => 'required|in:draft,posted,paid,void',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $this->calculateTotals();

        $refType = $this->type === 'credit_note' ? Sale::class : Purchase::class;

        $invoice = Invoice::updateOrCreate(['id' => $this->invoice_id], [
            'invoice_number' => $this->invoice_number,
            'type' => $this->type,
            'customer_id' => $this->type === 'credit_note' ? $this->customer_id : null,
            'supplier_id' => $this->type === 'debit_note' ? $this->supplier_id : null,
            'date' => $this->date,
            'due_date' => $this->due_date,
            'total_amount' => $this->total_amount,
            'tax_amount' => $this->tax_amount,
            'grand_total' => $this->grand_total,
            'status' => $this->status,
            'original_invoice_id' => $this->original_invoice_id ?: null,
            'reference_type' => $refType,
            'reference_id' => $this->original_invoice_id ?: null,
            'notes' => $this->notes,
            'created_by' => auth()->id() ?? 1,
        ]);

        // Clear and recreate items
        InvoiceItem::where('invoice_id', $invoice->id)->delete();
        foreach ($this->items as $item) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total_price' => $item['total_price'],
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => $this->isEditMode ? 'Update Credit/Debit Note' : 'Create Credit/Debit Note',
            'description' => "Credit/Debit Note {$this->invoice_number} saved."
        ]);

        session()->flash('success', 'Credit/Debit Note saved successfully.');
        $this->closeModal();
    }

    public function postInvoice($id)
    {
        $inv = Invoice::findOrFail($id);
        $inv->status = 'posted';
        $inv->save();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Post Credit/Debit Note',
            'description' => "Credit/Debit Note {$inv->invoice_number} posted to ledger."
        ]);

        session()->flash('success', 'Credit/Debit Note posted successfully.');
    }

    public function edit($id)
    {
        $inv = Invoice::with('items.product')->findOrFail($id);
        $this->invoice_id = $inv->id;
        $this->invoice_number = $inv->invoice_number;
        $this->type = $inv->type;
        $this->customer_id = $inv->customer_id;
        $this->supplier_id = $inv->supplier_id;
        $this->original_invoice_id = $inv->original_invoice_id;
        $this->date = $inv->date;
        $this->due_date = $inv->due_date;
        $this->total_amount = $inv->total_amount;
        $this->tax_amount = $inv->tax_amount;
        $this->grand_total = $inv->grand_total;
        $this->status = $inv->status;
        $this->notes = $inv->notes;

        $this->items = [];
        foreach ($inv->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $inv = Invoice::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Accounting',
            'action' => 'Delete Credit/Debit Note',
            'description' => "Credit/Debit Note {$inv->invoice_number} deleted."
        ]);

        $inv->delete();
        session()->flash('success', 'Credit/Debit Note deleted successfully.');
    }

    public function render()
    {
        $query = Invoice::with(['customer', 'supplier']);

        if ($this->search) {
            $query->where('invoice_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.invoice-manager', [
            'invoices' => $query->orderBy('date', 'desc')->paginate(10),
            'sales' => Sale::orderBy('invoice_number', 'desc')->get(),
            'purchases' => Purchase::orderBy('purchase_number', 'desc')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
