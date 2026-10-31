<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\SalesQuotation;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;

class SalesQuotationManager extends Component
{
    use WithPagination;

    public $search = '';

    public function create()
    {
        return $this->redirect(route('sales-quotations.create'), navigate: true);
    }

    public function edit($id)
    {
        return $this->redirect(route('sales-quotations.edit', $id), navigate: true);
    }

    public function delete($id)
    {
        $sq = SalesQuotation::findOrFail($id);
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Delete Quotation',
            'description' => 'Sales Quotation ' . $sq->sq_number . ' has been deleted.'
        ]);

        $sq->delete();
        session()->flash('success', 'Penawaran harga ' . $sq->sq_number . ' berhasil dihapus.');
    }

    public function convertToOrder($id)
    {
        $sq = SalesQuotation::findOrFail($id);
        $sq->status = 'converted';
        $sq->save();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Convert Quotation',
            'description' => 'Sales Quotation ' . $sq->sq_number . ' marked as converted.'
        ]);

        session()->flash('success', 'Quotation converted successfully. Go to Sales Orders to process.');
    }

    public function cancelQuotation($id)
    {
        $sq = SalesQuotation::findOrFail($id);
        $sq->status = 'canceled';
        $sq->save();

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Sales',
            'action' => 'Cancel Quotation',
            'description' => 'Sales Quotation ' . $sq->sq_number . ' marked as canceled.'
        ]);

        session()->flash('success', 'Quotation marked as canceled.');
    }

    public function render()
    {
        $query = SalesQuotation::with(['customer', 'items.product']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('sq_number', 'like', '%' . $this->search . '%')
                  ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function($sub) {
                      $sub->where('name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        return view('livewire.sales-quotation-manager', [
            'quotations' => $query->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
