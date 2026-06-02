<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Subscription;

class SubscriptionManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.subscription-manager', [
            'items' => Subscription::paginate(10)
        ])->layout('layouts.app');
    }
}
