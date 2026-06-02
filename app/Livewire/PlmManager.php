<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\WorkCenter;

class PlmManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.plm-manager', [
            'items' => WorkCenter::paginate(10)
        ])->layout('layouts.app');
    }
}
