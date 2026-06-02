<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Promotion;
use App\Models\Voucher;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use Illuminate\Support\Str;

class PromoManager extends Component
{
    public $activeTab = 'promos'; // promos | vouchers
    public $search = '';

    // Promo form
    public $showPromoModal = false;
    public $editingPromoId = null;
    public $promoName = '';
    public $promoDescription = '';
    public $promoType = 'percentage';
    public $promoValue = 0;
    public $promoBuyQty = 0;
    public $promoFreeQty = 0;
    public $promoMinPurchase = 0;
    public $promoMaxDiscount = '';
    public $promoCategoryId = '';
    public $promoMemberTier = '';
    public $promoStartDate = '';
    public $promoEndDate = '';
    public $promoStartTime = '';
    public $promoEndTime = '';
    public $promoActiveDays = [];
    public $promoStoreId = '';
    public $promoIsActive = true;

    // Voucher form
    public $showVoucherModal = false;
    public $editingVoucherId = null;
    public $voucherCode = '';
    public $voucherType = 'percentage';
    public $voucherValue = 0;
    public $voucherMinPurchase = 0;
    public $voucherMaxDiscount = '';
    public $voucherMaxUses = 1;
    public $voucherValidFrom = '';
    public $voucherValidUntil = '';
    public $voucherIsActive = true;
    public $generateBatch = false;
    public $batchCount = 10;

    // ── Promo CRUD ─────────────────────────────────────────
    public function openCreatePromo()
    {
        $this->resetPromoForm();
        $this->showPromoModal = true;
    }

    public function openEditPromo($id)
    {
        $p = Promotion::findOrFail($id);
        $this->editingPromoId = $p->id;
        $this->promoName = $p->name;
        $this->promoDescription = $p->description;
        $this->promoType = $p->type;
        $this->promoValue = $p->value;
        $this->promoBuyQty = $p->buy_qty;
        $this->promoFreeQty = $p->free_qty;
        $this->promoMinPurchase = $p->min_purchase;
        $this->promoMaxDiscount = $p->max_discount;
        $this->promoCategoryId = $p->category_id;
        $this->promoMemberTier = $p->member_tier;
        $this->promoStartDate = $p->start_date?->format('Y-m-d');
        $this->promoEndDate = $p->end_date?->format('Y-m-d');
        $this->promoStartTime = $p->start_time;
        $this->promoEndTime = $p->end_time;
        $this->promoActiveDays = $p->active_days ?? [];
        $this->promoStoreId = $p->store_id;
        $this->promoIsActive = $p->is_active;
        $this->showPromoModal = true;
    }

    public function savePromo()
    {
        $data = [
            'name' => $this->promoName,
            'description' => $this->promoDescription,
            'type' => $this->promoType,
            'value' => $this->promoValue,
            'buy_qty' => $this->promoBuyQty ?: null,
            'free_qty' => $this->promoFreeQty ?: null,
            'min_purchase' => $this->promoMinPurchase,
            'max_discount' => $this->promoMaxDiscount ?: null,
            'category_id' => $this->promoCategoryId ?: null,
            'member_tier' => $this->promoMemberTier ?: null,
            'start_date' => $this->promoStartDate ?: null,
            'end_date' => $this->promoEndDate ?: null,
            'start_time' => $this->promoStartTime ?: null,
            'end_time' => $this->promoEndTime ?: null,
            'active_days' => !empty($this->promoActiveDays) ? $this->promoActiveDays : null,
            'store_id' => $this->promoStoreId ?: null,
            'is_active' => $this->promoIsActive,
        ];

        if ($this->editingPromoId) {
            Promotion::find($this->editingPromoId)->update($data);
            session()->flash('success', 'Promo berhasil diperbarui!');
        } else {
            Promotion::create($data);
            session()->flash('success', 'Promo baru berhasil dibuat!');
        }

        $this->showPromoModal = false;
        $this->resetPromoForm();
    }

    public function deletePromo($id)
    {
        Promotion::findOrFail($id)->delete();
        session()->flash('success', 'Promo dihapus.');
    }

    public function togglePromo($id)
    {
        $p = Promotion::findOrFail($id);
        $p->update(['is_active' => !$p->is_active]);
    }

    // ── Voucher CRUD ───────────────────────────────────────
    public function openCreateVoucher()
    {
        $this->resetVoucherForm();
        $this->showVoucherModal = true;
    }

    public function openEditVoucher($id)
    {
        $v = Voucher::findOrFail($id);
        $this->editingVoucherId = $v->id;
        $this->voucherCode = $v->code;
        $this->voucherType = $v->type;
        $this->voucherValue = $v->value;
        $this->voucherMinPurchase = $v->min_purchase;
        $this->voucherMaxDiscount = $v->max_discount;
        $this->voucherMaxUses = $v->max_uses;
        $this->voucherValidFrom = $v->valid_from?->format('Y-m-d');
        $this->voucherValidUntil = $v->valid_until?->format('Y-m-d');
        $this->voucherIsActive = $v->is_active;
        $this->showVoucherModal = true;
    }

    public function saveVoucher()
    {
        if ($this->generateBatch && !$this->editingVoucherId) {
            for ($i = 0; $i < $this->batchCount; $i++) {
                Voucher::create([
                    'code' => strtoupper(Str::random(8)),
                    'type' => $this->voucherType,
                    'value' => $this->voucherValue,
                    'min_purchase' => $this->voucherMinPurchase,
                    'max_discount' => $this->voucherMaxDiscount ?: null,
                    'max_uses' => $this->voucherMaxUses,
                    'valid_from' => $this->voucherValidFrom ?: null,
                    'valid_until' => $this->voucherValidUntil ?: null,
                    'is_active' => $this->voucherIsActive,
                ]);
            }
            session()->flash('success', "{$this->batchCount} voucher berhasil di-generate!");
        } else {
            $data = [
                'code' => strtoupper($this->voucherCode ?: Str::random(8)),
                'type' => $this->voucherType,
                'value' => $this->voucherValue,
                'min_purchase' => $this->voucherMinPurchase,
                'max_discount' => $this->voucherMaxDiscount ?: null,
                'max_uses' => $this->voucherMaxUses,
                'valid_from' => $this->voucherValidFrom ?: null,
                'valid_until' => $this->voucherValidUntil ?: null,
                'is_active' => $this->voucherIsActive,
            ];

            if ($this->editingVoucherId) {
                Voucher::find($this->editingVoucherId)->update($data);
                session()->flash('success', 'Voucher berhasil diperbarui!');
            } else {
                Voucher::create($data);
                session()->flash('success', 'Voucher baru berhasil dibuat!');
            }
        }

        $this->showVoucherModal = false;
        $this->resetVoucherForm();
    }

    public function deleteVoucher($id)
    {
        Voucher::findOrFail($id)->delete();
        session()->flash('success', 'Voucher dihapus.');
    }

    private function resetPromoForm()
    {
        $this->editingPromoId = null;
        $this->promoName = '';
        $this->promoDescription = '';
        $this->promoType = 'percentage';
        $this->promoValue = 0;
        $this->promoBuyQty = 0;
        $this->promoFreeQty = 0;
        $this->promoMinPurchase = 0;
        $this->promoMaxDiscount = '';
        $this->promoCategoryId = '';
        $this->promoMemberTier = '';
        $this->promoStartDate = '';
        $this->promoEndDate = '';
        $this->promoStartTime = '';
        $this->promoEndTime = '';
        $this->promoActiveDays = [];
        $this->promoStoreId = '';
        $this->promoIsActive = true;
    }

    private function resetVoucherForm()
    {
        $this->editingVoucherId = null;
        $this->voucherCode = '';
        $this->voucherType = 'percentage';
        $this->voucherValue = 0;
        $this->voucherMinPurchase = 0;
        $this->voucherMaxDiscount = '';
        $this->voucherMaxUses = 1;
        $this->voucherValidFrom = '';
        $this->voucherValidUntil = '';
        $this->voucherIsActive = true;
        $this->generateBatch = false;
        $this->batchCount = 10;
    }

    public function render()
    {
        $promos = Promotion::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->latest()->paginate(15);

        $vouchers = Voucher::query()
            ->when($this->search, fn($q) => $q->where('code', 'like', "%{$this->search}%"))
            ->latest()->paginate(15);

        return view('livewire.promo-manager', [
            'promos' => $promos,
            'vouchers' => $vouchers,
            'categories' => ProductCategory::all(),
            'stores' => Store::where('is_active', true)->get(),
        ]);
    }
}
