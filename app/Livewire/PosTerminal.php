<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Product;
use App\Models\Member;
use App\Models\Store;
use App\Models\PosSession;
use App\Models\PosTransaction;
use App\Models\PosTransactionItem;
use App\Models\PosPendingOrder;
use App\Models\Voucher;
use App\Models\Promotion;
use App\Models\MemberPointLog;
use App\Models\StockItem;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Account;
use App\Models\Journal;
use App\Models\ActivityLog;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosTerminal extends Component
{
    // Search & Filter
    public $search = '';
    public $categoryFilter = '';

    // Cart
    public $cart = [];
    public $cartNote = '';

    // Member
    public $memberSearch = '';
    public $selectedMember = null;
    public $showMemberModal = false;
    public $newMemberName = '';
    public $newMemberPhone = '';
    public $newMemberEmail = '';

    // Payment
    public $showPaymentModal = false;
    public $paymentMethod = 'cash';
    public $cashReceived = 0;
    public $qrisReference = '';

    // Voucher
    public $voucherCode = '';
    public $appliedVoucher = null;
    public $voucherDiscount = 0;

    // Store & Session
    public $currentStore = null;
    public $currentSession = null;
    public $showSessionModal = false;
    public $openingCash = 0;
    public $closingCash = 0;

    // Pending Orders
    public $showPendingModal = false;
    public $pendingOrderName = '';

    // Receipt
    public $showReceiptModal = false;
    public $lastTransaction = null;

    // Barcode
    public $barcodeInput = '';
    public $showBarcodeCamera = false;

    // Notification Properties
    public $showNotification = false;
    public $notificationMessage = '';
    public $notificationType = 'success';
    public $notificationDetails = '';

    protected $listeners = ['barcodeScanned' => 'handleBarcodeScan'];

    public function mount()
    {
        $user = Auth::user();
        $this->currentStore = Store::query()->where('is_active', true)->first();

        if ($this->currentStore) {
            $this->currentSession = PosSession::query()
                ->where('user_id', $user->id)
                ->where('store_id', $this->currentStore->id)
                ->where('status', 'open')
                ->latest()
                ->first();
        }
    }

    // ── Session Management ─────────────────────────────────────
    public function openSession()
    {
        if (!$this->currentStore) {
            $this->showNotificationMessage('Pilih store/cabang terlebih dahulu.', 'error');
            return;
        }

        $this->currentSession = PosSession::query()->create([
            'user_id' => Auth::id(),
            'store_id' => $this->currentStore->id,
            'opening_cash' => $this->openingCash,
            'opened_at' => now(),
            'status' => 'open',
        ]);

        $this->showSessionModal = false;
        $this->showNotificationMessage('Shift berhasil dibuka!', 'success');
    }

    public function closeSession()
    {
        if (!$this->currentSession) return;

        $cashTrx = PosTransaction::query()
            ->where('pos_session_id', $this->currentSession->id)
            ->where('payment_method', 'cash')
            ->sum('grand_total');

        $expected = $this->currentSession->opening_cash + $cashTrx;

        $this->currentSession->update([
            'closing_cash' => $this->closingCash,
            'expected_cash' => $expected,
            'difference' => $this->closingCash - $expected,
            'total_transactions' => PosTransaction::query()->where('pos_session_id', $this->currentSession->id)->count(),
            'total_revenue' => PosTransaction::query()->where('pos_session_id', $this->currentSession->id)->sum('grand_total'),
            'closed_at' => now(),
            'status' => 'closed',
        ]);

        $this->currentSession = null;
        $this->showNotificationMessage('Shift berhasil ditutup (EOD)!', 'success');
    }

    // ── Notification Helper ────────────────────────────────────
    public function showNotificationMessage($message, $type = 'success', $details = '')
    {
        $this->showNotification = true;
        $this->notificationMessage = $message;
        $this->notificationType = $type;
        $this->notificationDetails = $details;
        
        // Auto hide after 3 seconds
        $this->dispatch('auto-hide-notification');
    }

    public function hideNotification()
    {
        $this->showNotification = false;
    }

    // ── Barcode ────────────────────────────────────────────────
    public function handleBarcodeScan($barcode)
    {
        $product = Product::query()->where('barcode', $barcode)->orWhere('code', $barcode)->first();
        if ($product) {
            $this->addToCart($product->id);
        } else {
            $this->showNotificationMessage("Produk dengan barcode '{$barcode}' tidak ditemukan.", 'error');
        }
    }

    public function scanBarcode()
    {
        if ($this->barcodeInput) {
            $this->handleBarcodeScan(trim($this->barcodeInput));
            $this->barcodeInput = '';
        }
    }

    // ── Cart Management ────────────────────────────────────────
    public function addToCart($productId)
    {
        $product = Product::query()->findOrFail($productId);
        $totalStock = StockItem::query()->where('product_id', $product->id)->sum('qty_on_hand');

        $key = (string) $productId;

        if (isset($this->cart[$key])) {
            if ($this->cart[$key]['qty'] + 1 > $totalStock && $totalStock > 0) {
                $this->showNotificationMessage("Stok tidak cukup. Tersedia: {$totalStock}", 'error');
                return;
            }
            $this->cart[$key]['qty']++;
            $this->cart[$key]['subtotal'] = $this->cart[$key]['qty'] * $this->cart[$key]['price'];
        } else {
            $this->cart[$key] = [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'price' => (float) $product->price,
                'qty' => 1,
                'unit' => $product->unit,
                'subtotal' => (float) $product->price,
                'discount' => 0,
            ];
        }

        $this->recalculate();
        $this->showNotificationMessage("{$product->name} ditambahkan ke cart.", 'success');
    }

    public function updateQty($productId, $qty)
    {
        $key = (string) $productId;
        $qty = max(0, (int) $qty);

        if ($qty <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        if (isset($this->cart[$key])) {
            $this->cart[$key]['qty'] = $qty;
            $this->cart[$key]['subtotal'] = $qty * $this->cart[$key]['price'];
        }

        $this->recalculate();
    }

    public function removeFromCart($productId)
    {
        $productName = $this->cart[(string) $productId]['name'] ?? 'Produk';
        unset($this->cart[(string) $productId]);
        $this->recalculate();
        $this->showNotificationMessage("{$productName} dihapus dari cart.", 'info');
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->selectedMember = null;
        $this->appliedVoucher = null;
        $this->voucherDiscount = 0;
        $this->voucherCode = '';
        $this->recalculate();
        $this->showNotificationMessage('Cart berhasil dikosongkan.', 'info');
    }

    // ── Calculations ───────────────────────────────────────────
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum('subtotal');
    }

    public function getMemberDiscountProperty()
    {
        if (!$this->selectedMember) return 0;
        $member = Member::find($this->selectedMember['id'] ?? 0);
        if (!$member) return 0;
        return round($this->subtotal * ($member->tier_discount / 100), 0);
    }

    public function getTotalDiscountProperty()
    {
        return $this->memberDiscount + $this->voucherDiscount;
    }

    public function getTaxAmountProperty()
    {
        $taxRate = $this->currentStore ? $this->currentStore->tax_rate : 11;
        return round(($this->subtotal - $this->totalDiscount) * ($taxRate / 100), 0);
    }

    public function getServiceChargeProperty()
    {
        if (!$this->currentStore || $this->currentStore->service_charge_rate <= 0) return 0;
        return round(($this->subtotal - $this->totalDiscount) * ($this->currentStore->service_charge_rate / 100), 0);
    }

    public function getGrandTotalProperty()
    {
        return $this->subtotal - $this->totalDiscount + $this->taxAmount + $this->serviceCharge;
    }

    public function getChangeProperty()
    {
        return max(0, $this->cashReceived - $this->grandTotal);
    }

    private function recalculate()
    {
        $this->cashReceived = max($this->cashReceived, 0);
    }

    // ── Member ─────────────────────────────────────────────────
    public function searchMember()
    {
        // Triggered via render
    }

    public function selectMember($memberId)
    {
        $member = Member::find($memberId);
        if ($member) {
            $this->selectedMember = $member->toArray();
            $this->memberSearch = '';
            $this->showNotificationMessage("Member {$member->name} dipilih.", 'success');
        }
    }

    public function removeMember()
    {
        $this->selectedMember = null;
        $this->showNotificationMessage('Member dihapus dari transaksi.', 'info');
    }

    public function createMember()
    {
        $code = 'MBR-' . now()->format('ymd') . '-' . sprintf('%04d', Member::count() + 1);

        $member = Member::create([
            'member_code' => $code,
            'name' => $this->newMemberName,
            'phone' => $this->newMemberPhone,
            'email' => $this->newMemberEmail,
            'store_id' => $this->currentStore?->id,
        ]);

        $this->selectedMember = $member->toArray();
        $this->showMemberModal = false;
        $this->newMemberName = '';
        $this->newMemberPhone = '';
        $this->newMemberEmail = '';
        $this->showNotificationMessage('Member baru berhasil didaftarkan!', 'success');
    }

    // ── Voucher ────────────────────────────────────────────────
    public function applyVoucher()
    {
        $voucher = Voucher::where('code', strtoupper(trim($this->voucherCode)))->first();

        if (!$voucher || !$voucher->isValid()) {
            $this->showNotificationMessage('Voucher tidak valid atau sudah kadaluarsa.', 'error');
            return;
        }

        $discount = $voucher->calculateDiscount($this->subtotal);
        if ($discount <= 0) {
            $this->showNotificationMessage('Belum memenuhi minimum pembelian voucher.', 'error');
            return;
        }

        $this->appliedVoucher = $voucher->toArray();
        $this->voucherDiscount = $discount;
        $this->showNotificationMessage("Voucher berhasil diaplikasikan! Diskon: Rp " . number_format($discount, 0, ',', '.'), 'success');
    }

    public function removeVoucher()
    {
        $this->appliedVoucher = null;
        $this->voucherDiscount = 0;
        $this->voucherCode = '';
        $this->showNotificationMessage('Voucher dihapus.', 'info');
    }

    // ── Pending Orders ─────────────────────────────────────────
    public function savePendingOrder()
    {
        if (empty($this->cart)) {
            $this->showNotificationMessage('Cart kosong.', 'error');
            return;
        }

        PosPendingOrder::create([
            'order_name' => $this->pendingOrderName ?: 'Order #' . (PosPendingOrder::count() + 1),
            'store_id' => $this->currentStore->id,
            'user_id' => Auth::id(),
            'member_id' => $this->selectedMember['id'] ?? null,
            'cart_data' => $this->cart,
            'notes' => $this->cartNote,
        ]);

        $this->clearCart();
        $this->pendingOrderName = '';
        $this->showPendingModal = false;
        $this->showNotificationMessage('Order berhasil disimpan sebagai tertunda!', 'success');
    }

    public function loadPendingOrder($orderId)
    {
        $order = PosPendingOrder::find($orderId);
        if (!$order) return;

        $this->cart = $order->cart_data;
        if ($order->member_id) {
            $member = Member::find($order->member_id);
            if ($member) $this->selectedMember = $member->toArray();
        }
        $this->cartNote = $order->notes ?? '';
        $order->update(['status' => 'completed']);
        $this->showPendingModal = false;
        $this->showNotificationMessage('Order tertunda berhasil dimuat!', 'success');
    }

    public function deletePendingOrder($orderId)
    {
        PosPendingOrder::where('id', $orderId)->update(['status' => 'cancelled']);
        $this->showNotificationMessage('Order tertunda dihapus.', 'info');
    }

    // ── Payment & Checkout ─────────────────────────────────────
    public function openPayment()
    {
        if (empty($this->cart)) {
            $this->showNotificationMessage('Cart kosong!', 'error');
            return;
        }
        $this->cashReceived = $this->grandTotal;
        $this->showPaymentModal = true;
    }

    public function processPayment()
    {
        if (empty($this->cart)) {
            $this->showNotificationMessage('Cart kosong!', 'error');
            return;
        }

        if ($this->paymentMethod === 'cash' && $this->cashReceived < $this->grandTotal) {
            $this->showNotificationMessage('Uang yang diterima kurang dari total belanja.', 'error');
            return;
        }

        DB::transaction(function () {
            $trxNum = 'TRX-' . ($this->currentStore ? $this->currentStore->code : 'HQ') . '-' . now()->format('ymdHis') . '-' . sprintf('%04d', PosTransaction::count() + 1);

            // Create POS Transaction
            $trx = PosTransaction::create([
                'transaction_number' => $trxNum,
                'store_id' => $this->currentStore->id ?? 1,
                'user_id' => Auth::id(),
                'pos_session_id' => $this->currentSession?->id,
                'member_id' => $this->selectedMember['id'] ?? null,
                'subtotal' => $this->subtotal,
                'discount_amount' => $this->totalDiscount,
                'discount_description' => $this->appliedVoucher ? 'Voucher: ' . ($this->appliedVoucher['code'] ?? '') : ($this->selectedMember ? 'Member discount' : null),
                'tax_amount' => $this->taxAmount,
                'service_charge' => $this->serviceCharge,
                'grand_total' => $this->grandTotal,
                'payment_method' => $this->paymentMethod,
                'cash_received' => $this->paymentMethod === 'cash' ? $this->cashReceived : $this->grandTotal,
                'cash_change' => $this->paymentMethod === 'cash' ? $this->change : 0,
                'qris_reference' => $this->paymentMethod === 'qris' ? $this->qrisReference : null,
                'voucher_id' => $this->appliedVoucher['id'] ?? null,
                'status' => 'completed',
            ]);

            // Create transaction items & reduce stock
            foreach ($this->cart as $item) {
                PosTransactionItem::create([
                    'pos_transaction_id' => $trx->id,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'product_code' => $item['code'],
                    'unit_price' => $item['price'],
                    'quantity' => $item['qty'],
                    'unit' => $item['unit'],
                    'discount' => $item['discount'] ?? 0,
                    'subtotal' => $item['subtotal'],
                ]);

                // Reduce stock
                $qtyToReduce = $item['qty'];
                $stocks = StockItem::where('product_id', $item['id'])->where('qty_on_hand', '>', 0)->get();
                foreach ($stocks as $stock) {
                    if ($qtyToReduce <= 0) break;
                    $reduction = min($qtyToReduce, $stock->qty_on_hand);
                    $stock->decrement('qty_on_hand', $reduction);
                    $qtyToReduce -= $reduction;

                    Inventory::create([
                        'product_id' => $item['id'],
                        'quantity' => -$reduction,
                        'type' => 'out',
                        'reference_type' => 'POS',
                        'reference_id' => $trx->id,
                        'notes' => "POS Transaction {$trxNum}",
                    ]);
                }

                $product = Product::find($item['id']);
                if ($product) $product->decrement('stock', $item['qty']);
            }

            // Update voucher usage
            if ($this->appliedVoucher) {
                Voucher::where('id', $this->appliedVoucher['id'])->increment('used_count');
            }

            // Member points (1 point per 10,000 IDR)
            if ($this->selectedMember) {
                $points = (int) floor($this->grandTotal / 10000);
                if ($points > 0) {
                    $member = Member::find($this->selectedMember['id']);
                    if ($member) {
                        $member->increment('total_points', $points);
                        $member->increment('total_spending', $this->grandTotal);
                        $member->checkTierUpgrade();

                        MemberPointLog::create([
                            'member_id' => $member->id,
                            'points' => $points,
                            'type' => 'earn',
                            'reference' => $trxNum,
                            'description' => "Earned from POS transaction",
                        ]);

                        $trx->update(['points_earned' => $points]);
                    }
                }
            }

            // Activity log
            ActivityLog::create([
                'user_id' => Auth::id(),
                'module' => 'POS',
                'action' => 'POS Transaction',
                'reference_id' => $trx->id,
                'description' => "POS: {$trxNum} — Rp " . number_format($this->grandTotal, 0, ',', '.'),
            ]);

            $this->lastTransaction = $trx->load('items');
        });

        $this->showPaymentModal = false;
        $this->showReceiptModal = true;

        // Show success notification
        $this->showNotificationMessage(
            'Transaksi Berhasil!',
            'success',
            'Nomor transaksi: ' . ($this->lastTransaction->transaction_number ?? 'N/A')
        );

        // Dispatch event for sound
        $this->dispatch('play-success-sound');

        // Reset
        $this->cart = [];
        $this->selectedMember = null;
        $this->appliedVoucher = null;
        $this->voucherDiscount = 0;
        $this->voucherCode = '';
        $this->cashReceived = 0;
        $this->qrisReference = '';
        $this->cartNote = '';
    }

    // ── Render ──────────────────────────────────────────────────
    public function render()
    {
        $productsQuery = Product::query()->where('stock', '>', 0);

        if ($this->search) {
            $productsQuery->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%")
                  ->orWhere('barcode', 'like', "%{$this->search}%");
            });
        }

        if ($this->categoryFilter) {
            $productsQuery->where('category_id', $this->categoryFilter);
        }

        $members = [];
        if ($this->memberSearch) {
            $members = Member::where('name', 'like', "%{$this->memberSearch}%")
                ->orWhere('phone', 'like', "%{$this->memberSearch}%")
                ->orWhere('member_code', 'like', "%{$this->memberSearch}%")
                ->limit(5)->get();
        }

        $pendingOrders = PosPendingOrder::where('store_id', $this->currentStore?->id ?? 0)
            ->where('status', 'pending')
            ->latest()->get();

        return view('livewire.pos-terminal', [
            'products' => $productsQuery->limit(24)->get(),
            'categories' => ProductCategory::all(),
            'memberResults' => $members,
            'pendingOrders' => $pendingOrders,
            'stores' => Store::where('is_active', true)->get(),
        ])->layout('layouts.pos');
    }
}