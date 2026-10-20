<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\ActivityLog;
use Illuminate\Validation\Rule;

class SalesManager extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $sale_id;
    public $invoice_number;
    public $customer_id;
    public $customer_name = '';
    public $customer_mode = 'select'; // 'select' or 'custom'
    public $sale_date;
    public $due_date;
    public $total_amount = 0;
    public $shipping_price = 0;
    public $discount_amount = 0;
    public $tax_type = 'pph_05'; // 'pph_05', 'ppn_11', 'none'
    public $tax_amount = 0;
    public $grand_total = 0;
    public $sale_status = 'draft';
    public $notes;
    public $items = [];
    public $isOpen = false;
    public $isEditMode = false;
    public $isPreview = false;

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function openPreview()
    {
        $this->calculateTotals();
        $this->isPreview = true;
    }

    public function closePreview()
    {
        $this->isPreview = false;
    }

    public function togglePreview()
    {
        $this->calculateTotals();
        $this->isPreview = !$this->isPreview;
    }

    public function resetInputFields()
    {
        $this->sale_id = null;
        $this->invoice_number = 'INV-' . now()->format('Ymd') . '-' . sprintf('%04d', Sale::count() + 1);
        $this->customer_id = '';
        $this->customer_name = '';
        $this->customer_mode = 'select';
        $this->sale_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(14)->format('Y-m-d');
        $this->total_amount = 0;
        $this->shipping_price = 0;
        $this->discount_amount = 0;
        $this->tax_type = 'pph_05';
        $this->tax_amount = 0;
        $this->grand_total = 0;
        $this->sale_status = 'draft';
        $this->notes = '';
        $this->items = [];
        $this->isEditMode = false;
        $this->isPreview = false;
        $this->resetValidation();
    }

    public function toggleCustomerMode()
    {
        $this->customer_mode = ($this->customer_mode === 'select') ? 'custom' : 'select';
        if ($this->customer_mode === 'custom') {
            $this->customer_id = null;
        } else {
            $this->customer_name = '';
        }
        $this->resetValidation(['customer_id', 'customer_name']);
    }

    public function updatedCustomerId($value)
    {
        if ($value) {
            $customer = Customer::find($value);
            if ($customer) {
                $this->customer_name = $customer->name;
            }
        } else {
            $this->customer_name = '';
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->addItem(); 
        $this->openModal();
    }

    public function addItem()
    {
        $this->items[] = [
            'product_id' => '',
            'product_name' => '',
            'model' => '',
            'is_custom' => false,
            'is_custom_total' => false,
            'is_rounded' => false,
            'quantity' => 1,
            'unit_price' => 0,
            'total' => 0
        ];
    }

    public function toggleProductMode($index)
    {
        if (isset($this->items[$index])) {
            $isCustom = !($this->items[$index]['is_custom'] ?? false);
            $this->items[$index]['is_custom'] = $isCustom;
            if ($isCustom) {
                $this->items[$index]['product_id'] = null;
            } else {
                $this->items[$index]['product_name'] = '';
                $this->items[$index]['model'] = '';
                $this->items[$index]['unit_price'] = 0;
            }
            $this->recalculateAllItems();
        }
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->recalculateAllItems();
    }

    public function updatedTotalAmount()
    {
        if ($this->total_amount === '' || $this->total_amount === null) {
            $this->total_amount = 0;
        }
        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function updatedShippingPrice()
    {
        if ($this->shipping_price === '' || $this->shipping_price === null) {
            $this->shipping_price = 0;
        }
        $this->recalculateGrandTotal();
    }

    public function updatedDiscountAmount()
    {
        if ($this->discount_amount === '' || $this->discount_amount === null) {
            $this->discount_amount = 0;
        }
        $this->recalculateGrandTotal();
    }

    public function updatedTaxType()
    {
        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function updatedTaxAmount()
    {
        if ($this->tax_amount === '' || $this->tax_amount === null) {
            $this->tax_amount = 0;
        }
        $this->recalculateGrandTotal();
    }

    public function setTaxType($type)
    {
        $this->tax_type = $type;
        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function handleItemFieldUpdated($index, $field, $value)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        if ($field === 'product_id' && $value) {
            $product = Product::find($value);
            if ($product) {
                $this->items[$index]['product_name'] = $product->name;
                $this->items[$index]['model'] = $product->sku ?: ($product->unit ?: '');
                $this->items[$index]['unit_price'] = floatval($product->price);
                $this->items[$index]['is_custom_total'] = false;
                $this->items[$index]['is_rounded'] = false;
                $qty = floatval($this->items[$index]['quantity'] ?? 1);
                $this->items[$index]['total'] = round($qty * floatval($product->price), 2);
            }
        } elseif ($field === 'total') {
            $newTotal = is_numeric($value) ? floatval($value) : 0;
            $this->items[$index]['total'] = $newTotal;
            $this->items[$index]['is_custom_total'] = true;
            $this->items[$index]['is_rounded'] = false;

            $qty = floatval($this->items[$index]['quantity'] ?? 0);
            if ($qty > 0) {
                $this->items[$index]['unit_price'] = round($newTotal / $qty, 2);
            }
        } elseif ($field === 'quantity') {
            $this->items[$index]['quantity'] = $value;
            $this->items[$index]['is_custom_total'] = false;
            $this->items[$index]['is_rounded'] = false;
            $qty = floatval($value);
            $price = floatval($this->items[$index]['unit_price'] ?? 0);
            $this->items[$index]['total'] = round($qty * $price, 2);
        } elseif ($field === 'unit_price') {
            $this->items[$index]['unit_price'] = $value;
            $this->items[$index]['is_custom_total'] = false;
            $this->items[$index]['is_rounded'] = false;
            $qty = floatval($this->items[$index]['quantity'] ?? 0);
            $price = floatval($value);
            $this->items[$index]['total'] = round($qty * $price, 2);
        }
    }

    public function roundValue(float $value): float
    {
        if ($value <= 0) {
            return 0;
        }
        if ($value >= 1000) {
            return round($value / 1000) * 1000;
        }
        if ($value >= 100) {
            return round($value / 100) * 100;
        }
        return round($value);
    }

    public function roundItemSubtotal($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $currentTotal = (isset($this->items[$index]['total']) && is_numeric($this->items[$index]['total']) && floatval($this->items[$index]['total']) > 0)
            ? floatval($this->items[$index]['total'])
            : (floatval($this->items[$index]['quantity'] ?? 0) * floatval($this->items[$index]['unit_price'] ?? 0));

        $rounded = $this->roundValue($currentTotal);
        $this->items[$index]['total'] = $rounded;
        $this->items[$index]['is_custom_total'] = false;
        $this->items[$index]['is_rounded'] = true;

        $qty = floatval($this->items[$index]['quantity'] ?? 0);
        if ($qty > 0) {
            $this->items[$index]['unit_price'] = round($rounded / $qty, 2);
        }

        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function autoItemSubtotal($index)
    {
        if (!isset($this->items[$index])) {
            return;
        }

        $qty = floatval($this->items[$index]['quantity'] ?? 0);
        $price = floatval($this->items[$index]['unit_price'] ?? 0);

        $this->items[$index]['is_custom_total'] = false;
        $this->items[$index]['is_rounded'] = false;
        $this->items[$index]['total'] = round($qty * $price, 2);

        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function roundAllSubtotals()
    {
        foreach ($this->items as $index => $item) {
            $this->roundItemSubtotal($index);
        }
    }

    public function autoAllSubtotals()
    {
        foreach ($this->items as $index => $item) {
            $this->autoItemSubtotal($index);
        }
    }

    public function roundDppSubtotal()
    {
        $val = is_numeric($this->total_amount) ? floatval($this->total_amount) : 0;
        if ($val > 0) {
            $this->total_amount = $this->roundValue($val);
        } else {
            $this->total_amount = $this->roundValue($this->getItemsSubtotal());
        }
        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function updated($propertyName, $value = null)
    {
        if ($propertyName === 'total_amount') {
            $this->updatedTotalAmount();
            return;
        }

        if ($propertyName === 'tax_type') {
            $this->recalculateTax();
            $this->recalculateGrandTotal();
            return;
        }

        if ($propertyName === 'shipping_price') {
            $this->updatedShippingPrice();
            return;
        }

        if ($propertyName === 'discount_amount') {
            $this->updatedDiscountAmount();
            return;
        }

        if ($propertyName === 'tax_amount') {
            $this->updatedTaxAmount();
            return;
        }

        if (str_starts_with($propertyName, 'items.')) {
            $parts = explode('.', $propertyName);
            if (count($parts) >= 3) {
                $index = $parts[1];
                $field = $parts[2];
                $this->handleItemFieldUpdated($index, $field, $value);
            }
            $this->recalculateAllItems();
            return;
        }
    }

    public function updatedItems($value, $name = null)
    {
        if ($name) {
            $parts = explode('.', $name);
            $field = end($parts);
            $index = $parts[count($parts) - 2] ?? 0;
            $this->handleItemFieldUpdated($index, $field, $value);
        }

        $this->recalculateAllItems();
    }

    public function getItemsSubtotal(): float
    {
        $sum = 0;
        foreach ($this->items as $item) {
            if (isset($item['total']) && is_numeric($item['total'])) {
                $sum += floatval($item['total']);
            } else {
                $sum += floatval($item['quantity'] ?? 0) * floatval($item['unit_price'] ?? 0);
            }
        }
        return $sum;
    }

    public function recalculateTax()
    {
        $itemsSubtotal = $this->getItemsSubtotal();
        // Dasar pengenaan pajak: jika ada DPP yang diisi > 0 gunakan DPP, jika tidak gunakan total product
        $base = (is_numeric($this->total_amount) && floatval($this->total_amount) > 0)
            ? floatval($this->total_amount)
            : $itemsSubtotal;

        if ($this->tax_type === 'pph_05') {
            $this->tax_amount = round($base * 0.005, 2);
        } elseif ($this->tax_type === 'ppn_12' || $this->tax_type === 'ppn_11') {
            $this->tax_amount = round($base * 0.12, 2);
        } else {
            $this->tax_amount = 0;
        }
    }

    public function recalculateGrandTotal()
    {
        // Grand total diambil dari Subtotal semua product secara langsung
        $productsSubtotal = $this->getItemsSubtotal();
        $discount = is_numeric($this->discount_amount) ? floatval($this->discount_amount) : 0;
        $tax = is_numeric($this->tax_amount) ? floatval($this->tax_amount) : 0;
        $shipping = is_numeric($this->shipping_price) ? floatval($this->shipping_price) : 0;

        $afterDiscount = max(0, $productsSubtotal - $discount);
        $this->grand_total = round($afterDiscount + $tax + $shipping, 2);
    }

    public function recalculateAllItems()
    {
        foreach ($this->items as $index => $item) {
            $qty = floatval($item['quantity'] ?? 0);
            $price = floatval($item['unit_price'] ?? 0);

            if (!empty($item['is_custom_total']) || !empty($item['is_rounded'])) {
                $this->items[$index]['total'] = is_numeric($item['total'] ?? null)
                    ? floatval($item['total'])
                    : round($qty * $price, 2);
            } else {
                $this->items[$index]['total'] = round($qty * $price, 2);
            }
        }

        // Subtotal (DPP) biarkan selalu 0 jika bukan mode edit atau jika belum diisi
        if ($this->total_amount === '' || $this->total_amount === null || (!$this->isEditMode && $this->total_amount == 0)) {
            $this->total_amount = 0;
        }

        $this->recalculateTax();
        $this->recalculateGrandTotal();
    }

    public function calculateTotals()
    {
        $this->recalculateAllItems();
    }

    public function store()
    {
        $rules = [
            'invoice_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('sales', 'invoice_number')->ignore($this->sale_id),
            ],
            'sale_date' => 'required|date',
            'due_date' => 'nullable|date',
            'sale_status' => 'required',
            'total_amount' => 'nullable|numeric|min:0',
            'shipping_price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'nullable|numeric|min:0',
            'items.*.model' => 'nullable|string|max:255',
        ];

        if ($this->customer_mode === 'custom') {
            $rules['customer_name'] = 'required|string|max:255';
        } else {
            $rules['customer_id'] = 'required|exists:customers,id';
        }

        foreach ($this->items as $index => $item) {
            if (!empty($item['is_custom'])) {
                $rules["items.{$index}.product_name"] = 'required|string|max:255';
            } else {
                $rules["items.{$index}.product_id"] = 'required|exists:products,id';
            }
        }

        $this->validate($rules, [
            'customer_name.required' => 'Nama customer wajib diisi.',
            'customer_id.required' => 'Customer wajib dipilih.',
            'items.*.product_name.required' => 'Nama produk wajib diisi.',
            'items.*.product_id.required' => 'Produk wajib dipilih.',
            'items.*.quantity.required' => 'Jumlah (Qty) wajib diisi.',
            'items.*.quantity.min' => 'Jumlah (Qty) minimal 0.01.',
            'items.*.unit_price.required' => 'Harga per unit wajib diisi.',
        ]);

        $customerId = $this->customer_mode === 'select' ? $this->customer_id : null;
        $customerName = $this->customer_mode === 'select'
            ? (Customer::find($this->customer_id)?->name ?? $this->customer_name)
            : $this->customer_name;

        $totalAmount = is_numeric($this->total_amount) ? floatval($this->total_amount) : 0;
        $shippingPrice = is_numeric($this->shipping_price) ? floatval($this->shipping_price) : 0;
        $discountAmount = is_numeric($this->discount_amount) ? floatval($this->discount_amount) : 0;
        $taxAmount = is_numeric($this->tax_amount) ? floatval($this->tax_amount) : 0;
        $grandTotal = is_numeric($this->grand_total) ? floatval($this->grand_total) : 0;

        $sale = Sale::updateOrCreate(['id' => $this->sale_id], [
            'invoice_number' => $this->invoice_number,
            'customer_id' => $customerId,
            'customer_name' => $customerName,
            'sale_date' => $this->sale_date,
            'due_date' => $this->due_date,
            'total_amount' => $totalAmount,
            'shipping_price' => $shippingPrice,
            'discount_amount' => $discountAmount,
            'tax_type' => $this->tax_type ?: 'pph_05',
            'tax_amount' => $taxAmount,
            'grand_total' => $grandTotal,
            'status' => $this->sale_status,
            'notes' => $this->notes,
        ]);

        if ($this->isEditMode) {
            SaleItem::where('sale_id', $sale->id)->delete();
        }

        foreach ($this->items as $item) {
            $isCustom = !empty($item['is_custom']);
            $productId = !$isCustom ? ($item['product_id'] ?: null) : null;
            $productName = $isCustom
                ? $item['product_name']
                : (Product::find($productId)?->name ?? ($item['product_name'] ?? ''));

            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $productId,
                'product_name' => $productName,
                'model' => $item['model'] ?? null,
                'quantity' => floatval($item['quantity'] ?? 0),
                'unit_price' => floatval($item['unit_price'] ?? 0),
                'total_price' => floatval($item['total'] ?? 0),
            ]);

            if ($this->sale_status === 'delivered' && $productId) {
                $product = Product::find($productId);
                if ($product) {
                    $product->decrement('stock', floatval($item['quantity'] ?? 0));
                }
            }
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'module' => 'Sales',
            'action' => $this->isEditMode ? 'Update Invoice' : 'Create Invoice',
            'description' => 'Invoice ' . $this->invoice_number . ' has been saved with total Rp ' . number_format($this->grand_total, 0, ',', '.')
        ]);

        session()->flash('success', 'Sale transaction saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $sale = Sale::with('items')->findOrFail($id);
        $this->sale_id = $sale->id;
        $this->invoice_number = $sale->invoice_number;
        $this->customer_id = $sale->customer_id;
        $this->customer_name = $sale->customer_name ?? ($sale->customer->name ?? '');
        $this->customer_mode = (empty($sale->customer_id) && !empty($sale->customer_name)) ? 'custom' : 'select';
        $this->sale_date = $sale->sale_date;
        $this->due_date = $sale->due_date;
        $this->total_amount = $sale->total_amount;
        $this->shipping_price = $sale->shipping_price ?? 0;
        $this->discount_amount = $sale->discount_amount ?? 0;
        $this->tax_type = $sale->tax_type ?? 'pph_05';
        $this->tax_amount = $sale->tax_amount;
        $this->grand_total = $sale->grand_total;
        $this->sale_status = $sale->status;
        $this->notes = $sale->notes;

        $this->items = [];
        foreach ($sale->items as $item) {
            $isCustom = empty($item->product_id) && !empty($item->product_name);
            $qty = floatval($item->quantity ?? 0);
            $unitPrice = floatval($item->unit_price ?? 0);
            $totalPrice = floatval($item->total_price ?? 0);
            $expectedTotal = round($qty * $unitPrice, 2);
            $isCustomTotal = abs($totalPrice - $expectedTotal) > 0.01;

            $this->items[] = [
                'product_id' => $item->product_id ?: '',
                'product_name' => $item->product_name ?? ($item->product->name ?? ''),
                'model' => $item->model ?? ($item->product->sku ?: ($item->product->unit ?: '')),
                'is_custom' => $isCustom,
                'is_custom_total' => $isCustomTotal,
                'is_rounded' => false,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $totalPrice
            ];
        }

        $this->isEditMode = true;
        $this->isPreview = false;
        $this->openModal();
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
