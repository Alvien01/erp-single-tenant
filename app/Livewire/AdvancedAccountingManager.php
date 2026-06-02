<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\DeferredEntry;
use App\Models\PaymentProvider;
use App\Models\PaymentTransaction;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class AdvancedAccountingManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'deferred'; // deferred, providers, transactions

    public function render()
    {
        $s = '%'.$this->search.'%';
        return view('livewire.advanced-accounting-manager', [
            'deferredEntries' => DeferredEntry::with(['account', 'recognitionAccount'])->where('name', 'like', $s)->paginate(10, ['*'], 'defPage'),
            'providers' => PaymentProvider::where('name', 'like', $s)->paginate(10, ['*'], 'provPage'),
            'transactions' => PaymentTransaction::with('provider')->where('transaction_number', 'like', $s)->orderByDesc('id')->paginate(10, ['*'], 'txPage'),
            'accounts' => Account::orderBy('code')->get(),
        ])->layout('layouts.app');
    }
}
