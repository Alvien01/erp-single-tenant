<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class PurchaseRequestManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $pr_id;
    public $department;
    public $status = 'draft';

    // Item rows
    public $items = [];

    protected $rules = [
        'department' => 'required|string|max:255',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|numeric|min:0.01',
        'items.*.notes' => 'nullable|string|max:255',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->pr_id = null;
        $this->department = '';
        $this->status = 'draft';
        $this->items = [
            ['product_id' => '', 'qty' => 1, 'notes' => '']
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
        $this->items[] = ['product_id' => '', 'qty' => 1, 'notes' => ''];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function store()
    {
        $this->validate();

        if (count($this->items) === 0) {
            session()->flash('error', 'Add at least one item.');
            return;
        }

        DB::transaction(function () {
            $pr = PurchaseRequest::updateOrCreate(
                ['id' => $this->pr_id],
                [
                    'pr_number' => $this->isEditMode ? PurchaseRequest::find($this->pr_id)->pr_number : 'PR-' . date('Ymd') . '-' . sprintf('%04d', PurchaseRequest::count() + 1),
                    'requested_by' => auth()->id() ?? 1,
                    'department' => $this->department,
                    'status' => $this->status,
                ]
            );

            if ($this->isEditMode) {
                $pr->items()->delete();
            }

            foreach ($this->items as $item) {
                PurchaseRequestItem::create([
                    'purchase_request_id' => $pr->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'notes' => $item['notes'] ?? '',
                ]);
            }

            ActivityLog::create([
                'user_id' => auth()->id() ?? 1,
                'module' => 'Procurement',
                'action' => $this->isEditMode ? 'Update Purchase Request' : 'Create Purchase Request',
                'description' => 'Purchase Request ' . $pr->pr_number . ' has been saved.'
            ]);
        });

        session()->flash('success', 'Purchase Request saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $pr = PurchaseRequest::with('items')->findOrFail($id);
        $this->pr_id = $pr->id;
        $this->department = $pr->department;
        $this->status = $pr->status;
        
        $this->items = [];
        foreach ($pr->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'qty' => $item->qty,
                'notes' => $item->notes,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function approveRequest($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->status = 'approved';
        $pr->save();

        session()->flash('success', 'Purchase Request approved.');
    }

    public function rejectRequest($id)
    {
        $pr = PurchaseRequest::findOrFail($id);
        $pr->status = 'rejected';
        $pr->save();

        session()->flash('success', 'Purchase Request rejected.');
    }

    public function render()
    {
        $query = PurchaseRequest::with(['requester', 'items.product']);

        if ($this->search) {
            $query->where('pr_number', 'like', '%' . $this->search . '%')
                  ->orWhere('department', 'like', '%' . $this->search . '%');
        }

        return view('livewire.purchase-request-manager', [
            'requests' => $query->orderBy('created_at', 'desc')->paginate(10),
            'products' => Product::all(),
        ]);
    }
}
