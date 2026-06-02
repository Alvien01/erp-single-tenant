<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Currency;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class CurrencyManager extends Component
{
    use WithPagination;

    public $search = '';
    public $isOpen = false;
    public $modalType = ''; // 'currency'

    // Form fields
    public $currency_id;
    public $code;
    public $name;
    public $symbol;
    public $exchange_rate = 1.0;

    // Converter calculator helper fields
    public $calc_amount = 100;
    public $calc_from_id;
    public $calc_to_id;
    public $calc_result = 0;
    public $calc_to_symbol = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModal($type)
    {
        $this->modalType = $type;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->modalType = '';
        $this->resetCurrencyFields();
    }

    public function resetCurrencyFields()
    {
        $this->currency_id = null;
        $this->code = '';
        $this->name = '';
        $this->symbol = '';
        $this->exchange_rate = 1.0;
    }

    public function createCurrency()
    {
        $this->resetCurrencyFields();
        $this->openModal('currency');
    }

    public function editCurrency($id)
    {
        $cur = Currency::query()->findOrFail($id);
        $this->currency_id = $cur->id;
        $this->code = $cur->code;
        $this->name = $cur->name;
        $this->symbol = $cur->symbol;
        $this->exchange_rate = $cur->exchange_rate;

        $this->openModal('currency');
    }

    public function saveCurrency()
    {
        $this->validate([
            'code' => 'required|string|max:3|unique:currencies,code,' . $this->currency_id,
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'exchange_rate' => 'required|numeric|min:0.000001',
        ]);

        Currency::query()->updateOrCreate(
            ['id' => $this->currency_id],
            [
                'code' => strtoupper($this->code),
                'name' => $this->name,
                'symbol' => $this->symbol,
                'exchange_rate' => $this->exchange_rate,
            ]
        );

        ActivityLog::query()->create([
            'user_id' => Auth::id() ?? 1,
            'module' => 'Multi-Currency',
            'action' => $this->currency_id ? 'Update Currency' : 'Create Currency',
            'description' => 'Saved currency: ' . $this->code . ' with rate ' . $this->exchange_rate
        ]);

        session()->flash('success', 'Currency settings saved successfully.');
        $this->closeModal();
    }

    public function deleteCurrency($id)
    {
        $cur = Currency::query()->findOrFail($id);
        if ($cur->code === 'IDR') {
            session()->flash('error', 'Base currency IDR cannot be deleted.');
            return;
        }
        $cur->delete();
        session()->flash('success', 'Currency deleted.');
    }

    public function syncRates()
    {
        try {
            $currencies = Currency::all();
            $codes = $currencies->pluck('code')->map(fn($c) => strtoupper($c))->toArray();
            
            if (!in_array('USD', $codes)) {
                $codes[] = 'USD';
            }
            if (!in_array('IDR', $codes)) {
                $codes[] = 'IDR';
            }

            $symbolsStr = implode(',', $codes);
            $apiKey = '65b4b525694d4f7cafc6cdb9bcd0b7c3';
            
            $response = Http::timeout(10)->get("https://api.currencyfreaks.com/v2.0/rates/latest", [
                'apikey' => $apiKey,
                'symbols' => $symbolsStr
            ]);

            if ($response->failed()) {
                session()->flash('error', 'Failed to fetch rates from CurrencyFreaks API.');
                return;
            }

            $data = $response->json();
            if (!isset($data['rates']['IDR'])) {
                session()->flash('error', 'Response from API did not contain IDR rate.');
                return;
            }

            $idrRate = floatval($data['rates']['IDR']);

            foreach ($currencies as $cur) {
                $codeUpper = strtoupper($cur->code);
                if ($codeUpper === 'IDR') {
                    $cur->exchange_rate = 1.0;
                } elseif ($codeUpper === 'USD') {
                    $cur->exchange_rate = $idrRate;
                } else {
                    if (isset($data['rates'][$codeUpper])) {
                        $symbolRateToUsd = floatval($data['rates'][$codeUpper]);
                        if ($symbolRateToUsd > 0) {
                            $cur->exchange_rate = $idrRate / $symbolRateToUsd;
                        }
                    }
                }
                $cur->save();
            }

            ActivityLog::query()->create([
                'user_id' => Auth::id() ?? 1,
                'module' => 'Multi-Currency',
                'action' => 'Sync Exchange Rates',
                'description' => 'Automatically synced exchange rates using CurrencyFreaks API. Base IDR/USD: Rp ' . number_format($idrRate, 2, ',', '.')
            ]);

            session()->flash('success', 'Exchange rates synced successfully via CurrencyFreaks API!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error syncing exchange rates: ' . $e->getMessage());
        }
    }

    public function calculateConversion()
    {
        $this->validate([
            'calc_amount' => 'required|numeric|min:0',
            'calc_from_id' => 'required|exists:currencies,id',
            'calc_to_id' => 'required|exists:currencies,id',
        ]);

        $from = Currency::query()->find($this->calc_from_id);
        $to = Currency::query()->find($this->calc_to_id);

        if ($from && $to) {
            // Amount in base currency (IDR) = amount * from_rate
            $amountInBase = $this->calc_amount * $from->exchange_rate;
            // Amount in target currency = amountInBase / to_rate
            $this->calc_result = $amountInBase / $to->exchange_rate;
            $this->calc_to_symbol = $to->symbol;
        }
    }

    public function render()
    {
        $query = Currency::query();

        if ($this->search) {
            $query->where('code', 'like', '%' . $this->search . '%')
                  ->orWhere('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.currency-manager', [
            'currencies' => $query->orderBy('code')->paginate(10),
            'allCurrencies' => Currency::all()
        ]);
    }
}
