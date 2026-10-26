<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;

class SalesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';

    public function create()
    {
        return $this->redirect(route('sales.create'), navigate: true);
    }

    public function edit($id)
    {
        return $this->redirect(route('sales.edit', $id), navigate: true);
    }

    public function delete($id)
    {
        $sale = Sale::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Sales',
            'action' => 'Delete Invoice',
            'description' => 'Invoice ' . $sale->invoice_number . ' has been deleted.'
        ]);

        $sale->delete();
        session()->flash('success', 'Invoice deleted successfully.');
    }

    public function render()
    {
        $query = Sale::with(['customer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->status) {
            $query->where('status', $this->status);
        }

        return view('livewire.sales-manager', [
            'sales' => $query->orderBy('invoice_number', 'desc')->paginate(10),
            'customers' => Customer::all(),
            'products' => Product::all(),
        ]);
    }
}
