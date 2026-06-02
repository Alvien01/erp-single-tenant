<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\RentalOrder;

class RentalManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.rental-manager', [
            'items' => RentalOrder::paginate(10)
        ])->layout('layouts.app');
    }
}
