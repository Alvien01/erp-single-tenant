<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Bom;
use App\Models\BomItem;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BOMManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $isEditMode = false;

    // Header fields
    public $bom_id;
    public $product_id;
    public $bom_version = '1.0';
    public $notes;

    // Item rows
    public $items = [];

    protected $rules = [
        'product_id' => 'required|exists:products,id',
        'bom_version' => 'required|string|max:50',
        'notes' => 'nullable|string|max:255',
        'items.*.component_id' => 'required|exists:products,id',
        'items.*.qty' => 'required|numeric|min:0.001',
        'items.*.unit' => 'required|string|max:50',
    ];

    public function mount()
    {
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->bom_id = null;
        $this->product_id = '';
        $this->bom_version = '1.0';
        $this->notes = '';
        $this->items = [
            ['component_id' => '', 'qty' => 1, 'unit' => 'pcs']
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
        $this->items[] = ['component_id' => '', 'qty' => 1, 'unit' => 'pcs'];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function updatedItems($value, $name)
    {
        if (str_contains($name, 'component_id')) {
            preg_match('/items\.(\d+)\.component_id/', $name, $matches);
            if (isset($matches[1])) {
                $index = $matches[1];
                $product = Product::query()->find($value);
                if ($product) {
                    $this->items[$index]['unit'] = $product->unit ?? 'pcs';
                }
            }
        }
    }

    public function store()
    {
        $this->validate();

        if (count($this->items) === 0) {
            session()->flash('error', 'Add at least one raw material item.');
            return;
        }

        DB::transaction(function () {
            $bom = Bom::query()->updateOrCreate(
                ['id' => $this->bom_id],
                [
                    'product_id' => $this->product_id,
                    'bom_version' => $this->bom_version,
                    'notes' => $this->notes ?: '',
                ]
            );

            if ($this->isEditMode) {
                $bom->items()->delete();
            }

            foreach ($this->items as $item) {
                BomItem::query()->create([
                    'bom_id' => $bom->id,
                    'component_id' => $item['component_id'],
                    'qty' => $item['qty'],
                    'unit' => $item['unit'],
                ]);
            }

            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'Manufacturing',
                'action' => $this->isEditMode ? 'Update BOM' : 'Create BOM',
                'description' => 'BOM for Product ' . ($bom->product->name ?? '') . ' saved successfully.'
            ]);
        });

        session()->flash('success', 'Bill of Materials saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $bom = Bom::with('items')->findOrFail($id);
        $this->bom_id = $bom->id;
        $this->product_id = $bom->product_id;
        $this->bom_version = $bom->bom_version;
        $this->notes = $bom->notes;
        
        $this->items = [];
        foreach ($bom->items as $item) {
            $this->items[] = [
                'component_id' => $item->component_id,
                'qty' => $item->qty,
                'unit' => $item->unit,
            ];
        }

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $bom = Bom::findOrFail($id);
        $bom->delete();
        session()->flash('success', 'BOM deleted successfully.');
    }

    public function render()
    {
        $query = Bom::with(['product', 'items.component']);

        if ($this->search) {
            $query->whereHas('product', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.b-o-m-manager', [
            'boms' => $query->orderBy('created_at', 'desc')->paginate(10),
            'products' => Product::all(),
        ]);
    }
}
