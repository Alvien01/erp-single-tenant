<?php

namespace App\Livewire;

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Rfq;
use App\Models\RfqItem;
use App\Models\PurchaseRequest;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class RFQManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $rfq_id;
    public $supplier_id;
    public $purchase_request_id;
    public $status = 'draft';

    // Item rows
    public $items = [];

    protected $rules = [
        'supplier_id' => 'required|exists:suppliers,id',
        'purchase_request_id' => 'nullable|exists:purchase_requests,id',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|numeric|min:0.01',
        'items.*.price_offered' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->rfq_id = null;
        $this->supplier_id = '';
        $this->purchase_request_id = '';
        $this->status = 'draft';
        $this->items = [
            ['product_id' => '', 'qty' => 1, 'price_offered' => 0]
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
        $this->items[] = ['product_id' => '', 'qty' => 1, 'price_offered' => 0];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedPurchaseRequestId($value)
    {
        if ($value) {
            $pr = PurchaseRequest::with('items.product')->find($value);
            if ($pr) {
                $this->items = [];
                foreach ($pr->items as $item) {
                    $this->items[] = [
                        'product_id' => $item->product_id,
                        'qty' => $item->qty,
                        'price_offered' => 0,
                    ];
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
            $rfq = Rfq::updateOrCreate(
                ['id' => $this->rfq_id],
                [
                    'rfq_number' => $this->isEditMode ? Rfq::find($this->rfq_id)->rfq_number : 'RFQ-' . date('Ymd') . '-' . sprintf('%04d', Rfq::count() + 1),
                    'supplier_id' => $this->supplier_id,
                    'purchase_request_id' => $this->purchase_request_id ?: null,
                    'created_by' => auth()->id() ?? 1,
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $rfq->items()->delete();
            }

            foreach ($this->items as $item) {
                RfqItem::create([
                    'rfq_id' => $rfq->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price_offered' => $item['price_offered'] ?? 0,
                ]);
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Procurement',
                'action' => $this->isEditMode ? 'Update RFQ' : 'Create RFQ',
                'description' => 'RFQ ' . $rfq->rfq_number . ' has been saved.'
            ]);
        });

        session()->flash('success', 'RFQ saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $rfq = Rfq::with('items')->findOrFail($id);
        $this->rfq_id = $rfq->id;
        $this->supplier_id = $rfq->supplier_id;
        $this->purchase_request_id = $rfq->purchase_request_id;
        $this->status = $rfq->status;
        
        $this->items = [];
        foreach ($rfq->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'price_offered' => $item->price_offered,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function generatePurchaseOrder($id)
    {
        $rfq = Rfq::with('items')->findOrFail($id);

        DB::transaction(function() use ($rfq) {
            $totalAmount = 0;
            foreach ($rfq->items as $item) {
                $totalAmount += ($item->qty * $item->price_offered);
            }

            $tax = $totalAmount * 0.11;
            $grand = $totalAmount + $tax;

            // Generate Purchase Order
            $purchase = Purchase::create([
                'purchase_number' => 'PO-' . date('Ymd') . '-' . sprintf('%04d', Purchase::count() + 1),
                'supplier_id' => $rfq->supplier_id,
                'purchase_date' => now()->format('Y-m-d'),
                'due_date' => now()->addDays(30)->format('Y-m-d'),
                'total_amount' => $totalAmount,
                'tax_amount' => $tax,
                'grand_total' => $grand,
                'status' => 'draft',
            ]);

            foreach ($rfq->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->qty,
                    'unit_price' => $item->price_offered,
                    'total_price' => ($item->qty * $item->price_offered)
                ]);
            }

            $rfq->status = 'responded';
            $rfq->save();

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Procurement',
                'action' => 'Convert RFQ to PO',
                'description' => 'Purchase Order ' . $purchase->purchase_number . ' generated from RFQ ' . $rfq->rfq_number
            ]);
        });

        session()->flash('success', 'Purchase Order generated successfully.');
    }

    public function render()
    {
        $query = Rfq::with(['supplier', 'items.product']);

        if ($this->search) {
            $query->whereHas('supplier', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            })->orWhere('rfq_number', 'like', '%' . $this->search . '%');
        }

        return view('livewire.r-f-q-manager', [
            'rfqs' => $query->orderBy('created_at', 'desc')->paginate(10),
            'suppliers' => Supplier::all(),
            'products' => Product::all(),
            'purchaseRequests' => PurchaseRequest::where('status', 'approved')->get(),
        ]);
    }
}
