<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesQuotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class SalesOrderManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $so_id;
    public $customer_id;
    public $sales_quotation_id;
    public $order_date;
    public $status = 'draft';

    // Item rows
    public $items = [];

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'sales_quotation_id' => 'nullable|exists:sales_quotations,id',
        'order_date' => 'required|date',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|numeric|min:0.01',
        'items.*.price' => 'required|numeric|min:0',
        'items.*.discount' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->so_id = null;
        $this->customer_id = '';
        $this->sales_quotation_id = '';
        $this->order_date = now()->format('Y-m-d');
        $this->status = 'draft';
        $this->items = [
            ['product_id' => '', 'qty' => 1, 'price' => 0, 'discount' => 0]
        ];
        $this->isEditMode = false;
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
    }

    public function addItem()
    {
        $this->items[] = ['product_id' => '', 'qty' => 1, 'price' => 0, 'discount' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedSalesQuotationId($value)
    {
        if ($value) {
            $sq = SalesQuotation::with('items.product')->find($value);
            if ($sq) {
                $this->customer_id = $sq->customer_id;
                $this->items = [];
                foreach ($sq->items as $item) {
                    $this->items[] = [
                        'product_id' => $item->product_id,
                        'qty' => $item->qty,
                        'price' => $item->price,
                        'discount' => $item->discount,
                    ];
                }
            }
        }
    }

    public function updated($name, $value)
    {
        if ($name === 'sales_quotation_id') {
            $this->updatedSalesQuotationId($value);
            return;
        }

        if (str_contains($name, 'items.') && str_contains($name, '.product_id')) {
            preg_match('/items\.(\d+)\.product_id/', $name, $matches);
            if (isset($matches[1])) {
                $index = $matches[1];
                $product = Product::find($value);
                if ($product) {
                    $this->items[$index]['price'] = $product->price;
                }
            }
        }
    }

    public function store()
    {
        $this->validate();

        if (count($this->items) === 0) {
            session()->flash('error', 'Add at least one item.');
            return;
        }

        DB::transaction(function () {
            $so = SalesOrder::updateOrCreate(
                ['id' => $this->so_id],
                [
                    'so_number' => $this->isEditMode ? SalesOrder::find($this->so_id)->so_number : 'SO-' . date('Ymd') . '-' . sprintf('%04d', SalesOrder::count() + 1),
                    'customer_id' => $this->customer_id,
                    'sales_quotation_id' => $this->sales_quotation_id ?: null,
                    'order_date' => $this->order_date,
                    'created_by' => auth()->id() ?? 1,
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $so->items()->delete();
            }

            foreach ($this->items as $item) {
                $subtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                SalesOrderItem::create([
                    'sales_order_id' => $so->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $subtotal
                ]);
            }

            // If quotation is selected, update its status
            if ($this->sales_quotation_id) {
                $sq = SalesQuotation::find($this->sales_quotation_id);
                if ($sq) {
                    $sq->status = 'converted';
                    $sq->save();
                }
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Sales',
                'action' => $this->isEditMode ? 'Update Sales Order' : 'Create Sales Order',
                'description' => 'Sales Order ' . $so->so_number . ' has been saved.'
            ]);
        });

        session()->flash('success', 'Sales Order saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $so = SalesOrder::with('items')->findOrFail($id);
        $this->so_id = $so->id;
        $this->customer_id = $so->customer_id;
        $this->sales_quotation_id = $so->sales_quotation_id;
        $this->order_date = $so->order_date;
        $this->status = $so->status;
        
        $this->items = [];
        foreach ($so->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'price' => $item->price,
                'discount' => $item->discount,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function generateInvoice($id)
    {
        $so = SalesOrder::with('items')->findOrFail($id);

        DB::transaction(function() use ($so) {
            // Check total amount
            $totalAmount = 0;
            foreach ($so->items as $item) {
                $totalAmount += $item->subtotal;
            }

            $tax = $totalAmount * 0.11;
            $grand = $totalAmount + $tax;

            // Generate invoice
            $sale = Sale::create([
                'invoice_number' => 'INV-' . date('Ymd') . '-' . sprintf('%04d', Sale::count() + 1),
                'customer_id' => $so->customer_id,
                'sale_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'grand_total' => $grand,
                'status' => 'draft',
                'notes' => 'Generated from Sales Order: ' . $so->so_number
            ]);

            foreach ($so->items as $item) {
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->qty,
                    'unit_price' => $item->price,
                    'total_price' => $item->subtotal
                ]);
            }

            $so->status = 'completed';
            $so->save();

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Sales',
                'action' => 'Convert SO to Invoice',
                'description' => 'Invoice ' . $sale->invoice_number . ' generated from SO ' . $so->so_number
            ]);
        });

        session()->flash('success', 'Invoice generated successfully.');
    }

    public function render()
    {
        $query = SalesOrder::with(['customer', 'items.product']);

        if ($this->search) {
            $query->whereHas('customer', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('so_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.sales-order-manager', [
            'orders' => $query->orderBy('created_at', 'desc')->paginate(10),
            'customers' => Customer::all(),
            'products' => Product::all(),
            'quotations' => SalesQuotation::where('status', '!=', 'converted')->get(),
        ]);
    }
}
