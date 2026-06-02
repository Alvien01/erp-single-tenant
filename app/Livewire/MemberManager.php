<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Member;
use App\Models\MemberPointLog;
use App\Models\PosTransaction;
use App\Models\Store;

class MemberManager extends Component
{
    public $search = '';
    public $showModal = false;
    public $editingId = null;
    public $showHistoryModal = false;
    public $historyMemberId = null;

    // Form
    public $name = '';
    public $phone = '';
    public $email = '';
    public $birth_date = '';
    public $tier = 'bronze';
    public $store_id = '';

    // Point adjustment
    public $showPointModal = false;
    public $pointMemberId = null;
    public $pointAmount = 0;
    public $pointType = 'adjust';
    public $pointDescription = '';

    public function openCreate()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit($id)
    {
        $m = Member::findOrFail($id);
        $this->editingId = $m->id;
        $this->name = $m->name;
        $this->phone = $m->phone;
        $this->email = $m->email;
        $this->birth_date = $m->birth_date?->format('Y-m-d');
        $this->tier = $m->tier;
        $this->store_id = $m->store_id;
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:30',
        ]);

        $data = [
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'birth_date' => $this->birth_date ?: null,
            'tier' => $this->tier,
            'store_id' => $this->store_id ?: null,
        ];

        if ($this->editingId) {
            Member::find($this->editingId)->update($data);
            session()->flash('success', 'Member berhasil diperbarui!');
        } else {
            $code = 'MBR-' . now()->format('ymd') . '-' . sprintf('%04d', Member::count() + 1);
            $data['member_code'] = $code;
            Member::create($data);
            session()->flash('success', 'Member baru berhasil dibuat!');
        }

        $this->showModal = false;
        $this->resetForm();
    }

    public function delete($id)
    {
        Member::findOrFail($id)->delete();
        session()->flash('success', 'Member dihapus.');
    }

    public function showHistory($id)
    {
        $this->historyMemberId = $id;
        $this->showHistoryModal = true;
    }

    public function openPointAdjust($id)
    {
        $this->pointMemberId = $id;
        $this->pointAmount = 0;
        $this->pointType = 'adjust';
        $this->pointDescription = '';
        $this->showPointModal = true;
    }

    public function adjustPoints()
    {
        $member = Member::findOrFail($this->pointMemberId);
        $pts = (int) $this->pointAmount;

        if ($this->pointType === 'redeem') $pts = -abs($pts);

        $member->increment('total_points', $pts);

        MemberPointLog::create([
            'member_id' => $member->id,
            'points' => $pts,
            'type' => $this->pointType,
            'description' => $this->pointDescription ?: 'Manual adjustment',
        ]);

        $this->showPointModal = false;
        session()->flash('success', 'Poin berhasil disesuaikan.');
    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->birth_date = '';
        $this->tier = 'bronze';
        $this->store_id = '';
    }

    public function render()
    {
        $members = Member::query()
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('member_code', 'like', "%{$this->search}%"))
            ->latest()
            ->paginate(15);

        $history = [];
        if ($this->historyMemberId) {
            $history = PosTransaction::where('member_id', $this->historyMemberId)->latest()->limit(20)->get();
        }

        $pointLogs = [];
        if ($this->pointMemberId) {
            $pointLogs = MemberPointLog::where('member_id', $this->pointMemberId)->latest()->limit(10)->get();
        }

        return view('livewire.member-manager', [
            'members' => $members,
            'stores' => Store::where('is_active', true)->get(),
            'transactionHistory' => $history,
            'pointLogs' => $pointLogs,
        ]);
    }
}
