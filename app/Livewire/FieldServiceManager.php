<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\FieldServiceOrder;
use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FieldServiceManager extends Component
{
    use WithPagination;

    public $search = '';

    public function render()
    {
        $s = '%'.$this->search.'%';
        return view('livewire.field-service-manager', [
            'orders' => FieldServiceOrder::with(['customer', 'technician'])->where('fsm_number', 'like', $s)->orderByDesc('id')->paginate(10),
            'customers' => Customer::orderBy('name')->get(),
            'projects' => Project::orderBy('name')->get(),
            'users' => User::orderBy('name')->get(),
        ])->layout('layouts.app');
    }
}
