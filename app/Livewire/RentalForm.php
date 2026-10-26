<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RentalOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RentalForm extends Component
{
    // Mode
    public $isEditMode = false;
    public $rental_id = null;

    // Form fields
    public $rental_number = '';
    public $customer_id = '';
    public $product_id = '';
    public $item_name = '';
    public $quantity = 1;
    public $rental_rate = 0;
    public $rate_unit = 'daily'; // hourly, daily, weekly, monthly
    public $duration = 1;
    public $start_date = '';
    public $end_date = '';
    public $return_date = null;
    public $deposit_amount = 0;
    public $delivery_fee = 0;
    public $total_amount = 0;
    public $status = 'draft'; // draft, confirmed, ongoing, returned, completed, cancelled
    public $payment_status = 'unpaid'; // unpaid, partially_paid, paid
    public $condition_out = '';
    public $condition_in = '';
    public $notes = '';

    protected function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'product_id' => 'nullable|exists:products,id',
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:1',
            'rental_rate' => 'required|numeric|min:0',
            'rate_unit' => 'required|in:hourly,daily,weekly,monthly',
            'duration' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'return_date' => 'nullable|date',
            'deposit_amount' => 'nullable|numeric|min:0',
            'delivery_fee' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,confirmed,ongoing,returned,completed,cancelled',
            'payment_status' => 'required|in:unpaid,partially_paid,paid',
            'condition_out' => 'nullable|string',
            'condition_in' => 'nullable|string',
            'notes' => 'nullable|string',
        ];
    }

    protected $messages = [
        'customer_id.required' => 'Penyewa / Customer wajib dipilih.',
        'customer_id.exists' => 'Customer yang dipilih tidak valid.',
        'item_name.required' => 'Nama barang/alat yang disewa wajib diisi.',
        'quantity.required' => 'Jumlah unit barang wajib diisi.',
        'quantity.min' => 'Jumlah unit barang minimal 1.',
        'rental_rate.required' => 'Tarif sewa wajib diisi.',
        'duration.required' => 'Durasi sewa wajib diisi.',
        'duration.min' => 'Durasi sewa minimal 1.',
        'start_date.required' => 'Tanggal mulai sewa wajib ditentukan.',
        'end_date.required' => 'Tanggal selesai sewa wajib ditentukan.',
        'end_date.after_or_equal' => 'Tanggal selesai sewa tidak boleh sebelum tanggal mulai.',
    ];

    public function mount($id = null)
    {
        if ($id) {
            $this->isEditMode = true;
            $this->rental_id = $id;
            $order = RentalOrder::findOrFail($id);

            $this->rental_number = $order->rental_number ?: 'RNT-' . str_pad($order->id, 5, '0', STR_PAD_LEFT);
            $this->customer_id = $order->customer_id;
            $this->product_id = $order->product_id;
            $this->item_name = $order->item_name;
            $this->quantity = $order->quantity;
            $this->rental_rate = $order->rental_rate;
            $this->rate_unit = $order->rate_unit ?: 'daily';
            $this->duration = $order->duration ?: 1;
            $this->start_date = $order->start_date ? $order->start_date->format('Y-m-d') : '';
            $this->end_date = $order->end_date ? $order->end_date->format('Y-m-d') : '';
            $this->return_date = $order->return_date ? $order->return_date->format('Y-m-d') : null;
            $this->deposit_amount = $order->deposit_amount;
            $this->delivery_fee = $order->delivery_fee;
            $this->total_amount = $order->total_amount;
            $this->status = $order->status;
            $this->payment_status = $order->payment_status ?: 'unpaid';
            $this->condition_out = $order->condition_out;
            $this->condition_in = $order->condition_in;
            $this->notes = $order->notes;
        } else {
            $this->isEditMode = false;
            $this->rental_number = $this->generateRentalNumber();
            $this->start_date = now()->format('Y-m-d');
            $this->end_date = now()->addDays(1)->format('Y-m-d');
            $this->calculateTotal();
        }
    }

    public function generateRentalNumber()
    {
        $date = now()->format('Ymd');
        $count = RentalOrder::whereDate('created_at', now()->toDateString())->count() + 1;
        return 'RNT-' . $date . '-' . sprintf('%04d', $count);
    }

    public function updatedProductId($value)
    {
        if ($value) {
            $product = Product::find($value);
            if ($product) {
                if (empty($this->item_name) || $this->item_name === '') {
                    $this->item_name = $product->name;
                }
                if ($this->rental_rate == 0) {
                    $this->rental_rate = $product->price ?? 0;
                }
            }
        }
        $this->calculateTotal();
    }

    public function updatedQuantity()
    {
        $this->calculateTotal();
    }

    public function updatedRentalRate()
    {
        $this->calculateTotal();
    }

    public function updatedDeliveryFee()
    {
        $this->calculateTotal();
    }

    public function updatedStartDate($value)
    {
        $this->recalculateEndDate();
    }

    public function updatedDuration($value)
    {
        $this->recalculateEndDate();
        $this->calculateTotal();
    }

    public function updatedRateUnit($value)
    {
        $this->recalculateEndDate();
        $this->calculateTotal();
    }

    public function recalculateEndDate()
    {
        if ($this->start_date && $this->duration > 0) {
            $start = Carbon::parse($this->start_date);
            $dur = intval($this->duration);

            if ($this->rate_unit === 'hourly') {
                $days = ceil($dur / 24);
                $this->end_date = $start->addDays(max(1, $days))->format('Y-m-d');
            } elseif ($this->rate_unit === 'daily') {
                $this->end_date = $start->addDays($dur)->format('Y-m-d');
            } elseif ($this->rate_unit === 'weekly') {
                $this->end_date = $start->addWeeks($dur)->format('Y-m-d');
            } elseif ($this->rate_unit === 'monthly') {
                $this->end_date = $start->addMonths($dur)->format('Y-m-d');
            }
        }
    }

    public function updatedEndDate($value)
    {
        if ($this->start_date && $value) {
            $start = Carbon::parse($this->start_date);
            $end = Carbon::parse($value);

            if ($end->greaterThanOrEqualTo($start)) {
                $diffDays = $start->diffInDays($end);
                if ($this->rate_unit === 'daily') {
                    $this->duration = max(1, $diffDays);
                } elseif ($this->rate_unit === 'weekly') {
                    $this->duration = max(1, ceil($diffDays / 7));
                } elseif ($this->rate_unit === 'monthly') {
                    $this->duration = max(1, ceil($diffDays / 30));
                }
                $this->calculateTotal();
            }
        }
    }

    public function calculateTotal()
    {
        $qty = floatval($this->quantity ?: 1);
        $rate = floatval($this->rental_rate ?: 0);
        $dur = intval($this->duration ?: 1);
        $delivery = floatval($this->delivery_fee ?: 0);

        $subtotal = $qty * $rate * $dur;
        $this->total_amount = round($subtotal + $delivery, 2);
    }

    public function save()
    {
        $this->calculateTotal();
        $this->validate();

        if (empty($this->rental_number)) {
            $this->rental_number = $this->generateRentalNumber();
        }

        // Auto set return_date if marked returned or completed
        if (in_array($this->status, ['returned', 'completed']) && empty($this->return_date)) {
            $this->return_date = now()->format('Y-m-d');
        }

        $order = RentalOrder::updateOrCreate(
            ['id' => $this->rental_id],
            [
                'rental_number' => $this->rental_number,
                'customer_id' => $this->customer_id,
                'product_id' => $this->product_id ?: null,
                'item_name' => $this->item_name,
                'quantity' => $this->quantity,
                'rental_rate' => $this->rental_rate,
                'rate_unit' => $this->rate_unit,
                'duration' => $this->duration,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'return_date' => $this->return_date,
                'deposit_amount' => $this->deposit_amount ?: 0,
                'delivery_fee' => $this->delivery_fee ?: 0,
                'total_amount' => $this->total_amount ?: 0,
                'status' => $this->status,
                'payment_status' => $this->payment_status,
                'condition_out' => $this->condition_out,
                'condition_in' => $this->condition_in,
                'notes' => $this->notes,
            ]
        );

        ActivityLog::create([
            'user_id' => Auth::id(),
            'module' => 'Rentals',
            'action' => $this->isEditMode ? 'Update Rental Order' : 'Create Rental Order',
            'description' => 'Rental Order ' . $order->rental_number . ' for ' . $order->item_name . ' (' . $order->status . ') has been saved via dedicated form view.'
        ]);

        session()->flash('success', 'Order rental ' . $order->rental_number . ' berhasil disimpan.');

        return $this->redirect(route('rentals'), navigate: true);
    }

    public function cancel()
    {
        return $this->redirect(route('rentals'), navigate: true);
    }

    public function render()
    {
        return view('livewire.rental-form', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
