<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\MarketingCampaign;

class MarketingManager extends Component
{
    public $search = '';
    
    public function render()
    {
        return view('livewire.marketing-manager', [
            'items' => MarketingCampaign::paginate(10)
        ])->layout('layouts.app');
    }
}
