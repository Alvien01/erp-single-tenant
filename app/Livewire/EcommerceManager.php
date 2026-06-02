<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EcommerceOrder;

class EcommerceManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.ecommerce-manager', [
            'items' => EcommerceOrder::paginate(10)
        ])->layout('layouts.app');
    }
}
