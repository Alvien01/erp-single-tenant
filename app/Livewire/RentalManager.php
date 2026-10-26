<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\RentalOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class RentalManager extends Component
{
    use WithPagination;

    // Filters
    public $search = '';
    public $statusFilter = '';
    public $paymentStatusFilter = '';

    // Modals state
    public $isDetailOpen = false;
    public $selectedRental = null;

    // Delete confirmation
    public $confirmingDeletion = false;
    public $deleteId = null;

    // Quick return modal
    public $confirmingReturn = false;
    public $returnRentalId = null;
    public $quickReturnDate = '';
    public $quickReturnCondition = '';

    public function mount()
    {
        $this->quickReturnDate = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingPaymentStatusFilter()
    {
        $this->resetPage();
    }

    public function create()
    {
        return $this->redirect(route('rentals.create'), navigate: true);
    }

    public function edit($id)
    {
        return $this->redirect(route('rentals.edit', $id), navigate: true);
    }

    public function viewDetail($id)
    {
        $this->selectedRental = RentalOrder::with(['customer', 'product'])->findOrFail($id);
        $this->isDetailOpen = true;
    }

    public function closeDetail()
    {
        $this->isDetailOpen = false;
        $this->selectedRental = null;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->confirmingDeletion = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $order = RentalOrder::find($this->deleteId);
            if ($order) {
                $rentalNo = $order->rental_number ?: ('#' . $order->id);
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'module' => 'Rentals',
                    'action' => 'Delete Rental Order',
                    'description' => 'Rental Order ' . $rentalNo . ' has been deleted.'
                ]);
                $order->delete();
                session()->flash('success', 'Order rental ' . $rentalNo . ' berhasil dihapus.');
            }
        }
        $this->confirmingDeletion = false;
        $this->deleteId = null;
    }

    public function quickStatusUpdate($id, $newStatus)
    {
        $order = RentalOrder::findOrFail($id);
        $oldStatus = $order->status;
        $order->status = $newStatus;

        if (in_array($newStatus, ['returned', 'completed']) && empty($order->return_date)) {
            $order->return_date = now()->format('Y-m-d');
        }

        $order->save();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Rentals',
            'action' => 'Update Rental Status',
            'description' => 'Status rental ' . $order->rental_number . ' diubah dari ' . $oldStatus . ' menjadi ' . $newStatus . '.'
        ]);

        session()->flash('success', 'Status sewa ' . $order->rental_number . ' diperbarui ke ' . ucfirst($newStatus) . '.');
    }

    public function openQuickReturn($id)
    {
        $this->returnRentalId = $id;
        $this->quickReturnDate = now()->format('Y-m-d');
        $this->quickReturnCondition = 'Barang diterima kembali dalam kondisi baik & lengkap.';
        $this->confirmingReturn = true;
    }

    public function processQuickReturn()
    {
        if ($this->returnRentalId) {
            $order = RentalOrder::find($this->returnRentalId);
            if ($order) {
                $order->status = 'returned';
                $order->return_date = $this->quickReturnDate ?: now()->format('Y-m-d');
                $order->condition_in = $this->quickReturnCondition;
                $order->save();

                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'module' => 'Rentals',
                    'action' => 'Return Rental Item',
                    'description' => 'Barang pada rental ' . $order->rental_number . ' telah dikembalikan.'
                ]);

                session()->flash('success', 'Pengembalian barang rental ' . $order->rental_number . ' berhasil diproses.');
            }
        }

        $this->confirmingReturn = false;
        $this->returnRentalId = null;
        $this->quickReturnCondition = '';
    }

    public function render()
    {
        $query = RentalOrder::with(['customer', 'product']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('rental_number', 'like', '%' . $this->search . '%')
                  ->orWhere('item_name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('customer', function ($c) {
                      $c->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('company_name', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->paymentStatusFilter) {
            $query->where('payment_status', $this->paymentStatusFilter);
        }

        // Summary metrics
        $totalRentals = RentalOrder::count();
        $ongoingCount = RentalOrder::where('status', 'ongoing')->count();
        $returnedCount = RentalOrder::whereIn('status', ['returned', 'completed'])->count();
        $totalRentalValue = RentalOrder::where('status', '!=', 'cancelled')->sum('total_amount');
        $activeDeposits = RentalOrder::where('status', 'ongoing')->sum('deposit_amount');

        return view('livewire.rental-manager', [
            'rentals' => $query->latest()->paginate(10),
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'totalRentals' => $totalRentals,
            'ongoingCount' => $ongoingCount,
            'returnedCount' => $returnedCount,
            'totalRentalValue' => $totalRentalValue,
            'activeDeposits' => $activeDeposits,
        ])->layout('layouts.app');
    }
}
