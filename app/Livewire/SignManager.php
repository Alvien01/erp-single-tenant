<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SignRequest;

class SignManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.sign-manager', [
            'items' => SignRequest::paginate(10)
        ])->layout('layouts.app');
    }
}
