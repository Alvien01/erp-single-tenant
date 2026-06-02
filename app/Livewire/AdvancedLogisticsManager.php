<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DeliveryCarrier;
use App\Models\DropshipOrder;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Product;

class AdvancedLogisticsManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'carriers'; // carriers, dropship

    // Carrier fields
    public $carrier_id, $carrier_name, $carrier_code, $provider, $tracking_url, $default_cost = 0, $margin_percent = 0, $carrier_is_active = true;

    // Dropship fields
    public $ds_id, $ds_number, $ds_sale_id, $ds_supplier_id, $ds_customer_id, $ds_status = 'draft', $ds_tracking_number, $ds_carrier_id, $ds_notes;
    public $ds_items = [];

    public $modalType = null;
    public $isEdit = false;

    public function updatingSearch() { $this->resetPage(); }
    public function closeModal() { $this->modalType = null; $this->isEdit = false; }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type; $this->isEdit = (bool) $id;
        if ($type === 'carrier') {
            if ($id) {
                $c = DeliveryCarrier::findOrFail($id);
                $this->carrier_id=$c->id; $this->carrier_name=$c->name; $this->carrier_code=$c->code;
                $this->provider=$c->provider; $this->tracking_url=$c->tracking_url;
                $this->default_cost=$c->default_cost; $this->margin_percent=$c->margin_percent; $this->carrier_is_active=$c->is_active;
            } else {
                $this->carrier_id=null; $this->carrier_name=''; $this->carrier_code=''; $this->provider='';
                $this->tracking_url=''; $this->default_cost=0; $this->margin_percent=0; $this->carrier_is_active=true;
            }
        } elseif ($type === 'dropship') {
            if ($id) {
                $d = DropshipOrder::with('items')->findOrFail($id);
                $this->ds_id=$d->id; $this->ds_number=$d->dropship_number; $this->ds_supplier_id=$d->supplier_id;
                $this->ds_customer_id=$d->customer_id; $this->ds_status=$d->status;
                $this->ds_tracking_number=$d->tracking_number; $this->ds_carrier_id=$d->carrier_id; $this->ds_notes=$d->notes;
                $this->ds_items = $d->items->map(fn($i) => ['product_id'=>$i->product_id,'qty'=>$i->qty,'unit_price'=>$i->unit_price])->toArray();
                if (empty($this->ds_items)) $this->ds_items = [['product_id'=>'','qty'=>1,'unit_price'=>0]];
            } else {
                $this->ds_id=null; $this->ds_number='DS-'.date('Ymd').'-'.str_pad(DropshipOrder::count()+1,4,'0',STR_PAD_LEFT);
                $this->ds_supplier_id=''; $this->ds_customer_id=''; $this->ds_status='draft';
                $this->ds_tracking_number=''; $this->ds_carrier_id=''; $this->ds_notes='';
                $this->ds_items = [['product_id'=>'','qty'=>1,'unit_price'=>0]];
            }
        }
    }

    public function addDsItem() { $this->ds_items[] = ['product_id'=>'','qty'=>1,'unit_price'=>0]; }
    public function removeDsItem($i) { unset($this->ds_items[$i]); $this->ds_items = array_values($this->ds_items); }

    public function saveCarrier()
    {
        $this->validate(['carrier_name'=>'required|string','carrier_code'=>'required|string|unique:delivery_carriers,code,'.$this->carrier_id]);
        DeliveryCarrier::updateOrCreate(['id'=>$this->carrier_id], [
            'name'=>$this->carrier_name,'code'=>$this->carrier_code,'provider'=>$this->provider,
            'tracking_url'=>$this->tracking_url,'default_cost'=>$this->default_cost,'margin_percent'=>$this->margin_percent,'is_active'=>$this->carrier_is_active,
        ]);
        session()->flash('success','Carrier saved!'); $this->closeModal();
    }

    public function saveDropship()
    {
        $this->validate(['ds_supplier_id'=>'required|exists:suppliers,id','ds_customer_id'=>'required|exists:customers,id']);
        $order = DropshipOrder::updateOrCreate(['id'=>$this->ds_id], [
            'dropship_number'=>$this->ds_number,'supplier_id'=>$this->ds_supplier_id,'customer_id'=>$this->ds_customer_id,
            'status'=>$this->ds_status,'tracking_number'=>$this->ds_tracking_number,'carrier_id'=>$this->ds_carrier_id ?: null,'notes'=>$this->ds_notes,
        ]);
        $order->items()->delete();
        foreach ($this->ds_items as $item) {
            if (!empty($item['product_id'])) {
                $order->items()->create(['product_id'=>$item['product_id'],'qty'=>$item['qty'],'unit_price'=>$item['unit_price'],'subtotal'=>$item['qty']*$item['unit_price']]);
            }
        }
        session()->flash('success','Dropship order saved!'); $this->closeModal();
    }

    public function delete($type, $id)
    {
        match($type) { 'carrier'=>DeliveryCarrier::findOrFail($id)->delete(), 'dropship'=>DropshipOrder::findOrFail($id)->delete() };
        session()->flash('success', ucfirst($type) . ' deleted!');
    }

    public function render()
    {
        $s = '%'.$this->search.'%';
        return view('livewire.advanced-logistics-manager', [
            'carriers' => DeliveryCarrier::where('name','like',$s)->paginate(10,['*'],'carriersPage'),
            'dropships' => DropshipOrder::with(['supplier','customer','carrier'])->where('dropship_number','like',$s)->orderByDesc('id')->paginate(10,['*'],'dsPage'),
            'suppliers' => Supplier::orderBy('name')->get(),
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'allCarriers' => DeliveryCarrier::where('is_active',true)->get(),
        ])->layout('layouts.app');
    }
}
