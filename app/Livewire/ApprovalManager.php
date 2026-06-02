<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApprovalRule;
use App\Models\Approval;
use App\Models\ActivityLog;
use App\Models\User;

class ApprovalManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'requests'; // requests, rules

    // Rule Form Fields
    public $rule_id;
    public $document_type = 'Purchase'; // Sale, Purchase, Transfer, Expenses
    public $min_amount = 0;
    public $max_amount = 999999999;
    public $approver_role = 'manager'; // manager, finance, admin
    public $sequence = 1;
    public $is_active = true;

    // Approval Decision Modal fields
    public $isOpenDecisionModal = false;
    public $selectedApprovalId;
    public $decision_status = 'approved'; // approved, rejected
    public $decision_notes;

    public $isOpenRuleModal = false;
    public $isEditRuleMode = false;

    public function openRuleModal()
    {
        $this->isOpenRuleModal = true;
    }

    public function closeRuleModal()
    {
        $this->isOpenRuleModal = false;
        $this->resetRuleFields();
    }

    public function resetRuleFields()
    {
        $this->rule_id = null;
        $this->document_type = 'Purchase';
        $this->min_amount = 0;
        $this->max_amount = 999999999;
        $this->approver_role = 'manager';
        $this->sequence = 1;
        $this->is_active = true;
        $this->isEditRuleMode = false;
    }

    public function createRule()
    {
        $this->resetRuleFields();
        $this->openRuleModal();
    }

    public function storeRule()
    {
        $this->validate([
            'document_type' => 'required|string',
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'required|numeric|gt:min_amount',
            'approver_role' => 'required|string',
            'sequence' => 'required|integer|min:1',
            'is_active' => 'required|boolean',
        ]);

        ApprovalRule::updateOrCreate(['id' => $this->rule_id], [
            'module_type' => $this->document_type,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'role_required' => $this->approver_role,
            'sequence' => $this->sequence,
            'is_active' => $this->is_active,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'System',
            'action' => $this->isEditRuleMode ? 'Update Approval Rule' : 'Create Approval Rule',
            'description' => "Approval Rule for {$this->document_type} (Role: {$this->approver_role}) saved."
        ]);

        session()->flash('success', 'Approval Rule saved successfully.');
        $this->closeRuleModal();
    }

    public function editRule($id)
    {
        $rule = ApprovalRule::findOrFail($id);
        $this->rule_id = $rule->id;
        $this->document_type = $rule->module_type;
        $this->min_amount = $rule->min_amount;
        $this->max_amount = $rule->max_amount;
        $this->approver_role = $rule->role_required;
        $this->sequence = $rule->sequence;
        $this->is_active = $rule->is_active;

        $this->isEditRuleMode = true;
        $this->openRuleModal();
    }

    public function deleteRule($id)
    {
        ApprovalRule::findOrFail($id)->delete();
        session()->flash('success', 'Approval Rule deleted.');
    }

    // Requests Actions
    public function openDecision($id)
    {
        $this->selectedApprovalId = $id;
        $this->decision_status = 'approved';
        $this->decision_notes = '';
        $this->isOpenDecisionModal = true;
    }

    public function closeDecision()
    {
        $this->isOpenDecisionModal = false;
        $this->selectedApprovalId = null;
    }

    public function storeDecision()
    {
        $this->validate([
            'decision_status' => 'required|in:approved,rejected',
            'decision_notes' => 'nullable|string',
        ]);

        $app = Approval::with('rule')->findOrFail($this->selectedApprovalId);
        $app->status = $this->decision_status;
        $app->approver_id = auth()->id() ?? 1;
        $app->approved_at = now();
        $app->notes = $this->decision_notes;
        $app->save();

        // Dynamically update the reference document status based on approval/rejection!
        $ref = $app->reference;
        if ($ref) {
            if ($this->decision_status === 'approved') {
                $ref->status = 'confirmed'; // or 'approved'
            } else {
                $ref->status = 'cancelled';
            }
            $ref->save();
        }

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'System',
            'action' => 'Approval Decision',
            'description' => "Request #{$app->id} marked as {$this->decision_status}."
        ]);

        session()->flash('success', "Approval status updated to {$this->decision_status}.");
        $this->closeDecision();
    }

    public function render()
    {
        return view('livewire.approval-manager', [
            'rules' => ApprovalRule::orderBy('module_type')->orderBy('sequence')->paginate(10),
            'requests' => Approval::with(['rule', 'requester', 'approver'])->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
