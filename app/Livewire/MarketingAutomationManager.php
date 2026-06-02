<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AutomationWorkflow;
use App\Models\Survey;
use Illuminate\Support\Facades\Auth;

class MarketingAutomationManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'workflows'; // workflows, surveys

    public function render()
    {
        $s = '%'.$this->search.'%';
        return view('livewire.marketing-automation-manager', [
            'workflows' => AutomationWorkflow::with('steps')->where('name', 'like', $s)->paginate(10, ['*'], 'wfPage'),
            'surveys' => Survey::withCount(['questions', 'responses'])->where('title', 'like', $s)->paginate(10, ['*'], 'srvPage'),
        ])->layout('layouts.app');
    }
}
