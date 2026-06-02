<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Company;
use App\Models\InterCompanyRule;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class MultiCompanyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $activeTab = 'companies'; // companies, rules

    // Company Form
    public $company_id, $code, $name, $address, $phone, $email, $tax_number, $currency_code = 'IDR', $parent_company_id, $is_active = true;

    // Rule Form
    public $rule_id, $source_company_id, $target_company_id, $rule_type = 'purchase_to_sale', $auto_create = true, $rule_is_active = true;

    public $modalType = null;
    public $isEdit = false;

    public function updatingSearch() { $this->resetPage(); }

    public function openModal($type, $id = null)
    {
        $this->modalType = $type;
        $this->isEdit = (bool) $id;

        if ($type === 'company') {
            if ($id) {
                $c = Company::findOrFail($id);
                $this->company_id = $c->id; $this->code = $c->code; $this->name = $c->name;
                $this->address = $c->address; $this->phone = $c->phone; $this->email = $c->email;
                $this->tax_number = $c->tax_number; $this->currency_code = $c->currency_code ?? 'IDR';
                $this->parent_company_id = $c->parent_company_id; $this->is_active = $c->is_active;
            } else {
                $this->resetCompanyFields();
            }
        } elseif ($type === 'rule') {
            if ($id) {
                $r = InterCompanyRule::findOrFail($id);
                $this->rule_id = $r->id; $this->source_company_id = $r->source_company_id;
                $this->target_company_id = $r->target_company_id; $this->rule_type = $r->rule_type;
                $this->auto_create = $r->auto_create; $this->rule_is_active = $r->is_active;
            } else {
                $this->resetRuleFields();
            }
        }
    }

    public function closeModal() { $this->modalType = null; $this->isEdit = false; }

    private function resetCompanyFields()
    {
        $this->company_id = null; $this->code = ''; $this->name = ''; $this->address = '';
        $this->phone = ''; $this->email = ''; $this->tax_number = ''; $this->currency_code = 'IDR';
        $this->parent_company_id = null; $this->is_active = true;
    }

    private function resetRuleFields()
    {
        $this->rule_id = null; $this->source_company_id = ''; $this->target_company_id = '';
        $this->rule_type = 'purchase_to_sale'; $this->auto_create = true; $this->rule_is_active = true;
    }

    public function saveCompany()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|unique:companies,code,' . $this->company_id,
        ]);

        Company::updateOrCreate(['id' => $this->company_id], [
            'code' => $this->code, 'name' => $this->name, 'address' => $this->address,
            'phone' => $this->phone, 'email' => $this->email, 'tax_number' => $this->tax_number,
            'currency_code' => $this->currency_code, 'parent_company_id' => $this->parent_company_id ?: null,
            'is_active' => $this->is_active,
        ]);

        ActivityLog::create(['user_id' => Auth::id(), 'module' => 'Multi-Company', 'action' => $this->isEdit ? 'update' : 'create', 'description' => ($this->isEdit ? 'Updated' : 'Created') . ' company: ' . $this->name]);
        session()->flash('success', 'Company saved successfully!');
        $this->closeModal();
    }

    public function saveRule()
    {
        $this->validate([
            'source_company_id' => 'required|exists:companies,id',
            'target_company_id' => 'required|exists:companies,id|different:source_company_id',
            'rule_type' => 'required|in:purchase_to_sale,sale_to_purchase,transfer',
        ]);

        InterCompanyRule::updateOrCreate(['id' => $this->rule_id], [
            'source_company_id' => $this->source_company_id, 'target_company_id' => $this->target_company_id,
            'rule_type' => $this->rule_type, 'auto_create' => $this->auto_create, 'is_active' => $this->rule_is_active,
        ]);

        session()->flash('success', 'Inter-company rule saved!');
        $this->closeModal();
    }

    public function deleteCompany($id) { Company::findOrFail($id)->delete(); session()->flash('success', 'Company deleted.'); }
    public function deleteRule($id) { InterCompanyRule::findOrFail($id)->delete(); session()->flash('success', 'Rule deleted.'); }

    public function render()
    {
        $companies = Company::where('name', 'like', '%' . $this->search . '%')->paginate(10);
        $rules = InterCompanyRule::with(['sourceCompany', 'targetCompany'])->paginate(10);
        $allCompanies = Company::orderBy('name')->get();

        return view('livewire.multi-company-manager', compact('companies', 'rules', 'allCompanies'))->layout('layouts.app');
    }
}
