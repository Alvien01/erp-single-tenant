<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SalesQuotation;
use App\Models\SalesQuotationItem;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class SalesQuotationManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $sq_id;
    public $customer_id;
    public $valid_until;
    public $status = 'draft';

    // Item rows
    public $items = [];

    protected $rules = [
        'customer_id' => 'required|exists:customers,id',
        'valid_until' => 'required|date',
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
        $this->sq_id = null;
        $this->customer_id = '';
        $this->valid_until = now()->addDays(14)->format('Y-m-d');
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

    public function updatedItems($value, $name)
    {
        if (str_contains($name, 'product_id')) {
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
            $sq = SalesQuotation::updateOrCreate(
                ['id' => $this->sq_id],
                [
                    'sq_number' => $this->isEditMode ? SalesQuotation::find($this->sq_id)->sq_number : 'SQ-' . date('Ymd') . '-' . sprintf('%04d', SalesQuotation::count() + 1),
                    'customer_id' => $this->customer_id,
                    'valid_until' => $this->valid_until,
                    'created_by' => auth()->id() ?? 1,
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $sq->items()->delete();
            }

            foreach ($this->items as $item) {
                $subtotal = ($item['qty'] * $item['price']) - ($item['discount'] ?? 0);
                SalesQuotationItem::create([
                    'sales_quotation_id' => $sq->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $subtotal
                ]);
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Sales',
                'action' => $this->isEditMode ? 'Update Quotation' : 'Create Quotation',
                'description' => 'Sales Quotation ' . $sq->sq_number . ' has been saved.'
            ]);
        });

        session()->flash('success', 'Sales Quotation saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $sq = SalesQuotation::with('items')->findOrFail($id);
        $this->sq_id = $sq->id;
        $this->customer_id = $sq->customer_id;
        $this->valid_until = $sq->valid_until;
        $this->status = $sq->status;
        
        $this->items = [];
        foreach ($sq->items as $item) {
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

    public function convertToOrder($id)
    {
        $sq = SalesQuotation::findOrFail($id);
        $sq->status = 'converted';
        $sq->save();

        session()->flash('success', 'Quotation converted successfully. Go to Sales Orders to process.');
    }

    public function cancelQuotation($id)
    {
        $sq = SalesQuotation::findOrFail($id);
        $sq->status = 'canceled';
        $sq->save();

        session()->flash('success', 'Quotation marked as canceled.');
    }

    public function render()
    {
        $query = SalesQuotation::with(['customer', 'items.product']);

        if ($this->search) {
            $query->whereHas('customer', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('sq_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.sales-quotation-manager', [
            'quotations' => $query->orderBy('created_at', 'desc')->paginate(10),
            'customers' => Customer::all(),
            'products' => Product::all(),
        ]);
    }
}
