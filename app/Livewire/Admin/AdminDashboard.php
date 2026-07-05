<?php

namespace App\Livewire\Admin;

use App\Models\DeliveryJob;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Voucher;
use App\Services\AdminDashboardDataService;
use App\Services\OverdueService;
use App\Services\TimeService;
use Livewire\Component;
use Livewire\WithPagination;

class AdminDashboard extends Component
{
    use WithPagination;

    public string $tab = 'monitoring';

    // --- Voucher form ---
    public string $voucherCode = '';
    public string $voucherType = 'percent';
    public $voucherValue = '';
    public string $voucherExpiry = '';
    public $voucherLimit = 10;

    // --- Promo form ---
    public string $promoCode = '';
    public string $promoType = 'percent';
    public $promoValue = '';
    public string $promoExpiry = '';

    public string $lastOverdueSummary = '';

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetErrorBag();
    }

    public function createVoucher()
    {
        $this->validate([
            'voucherCode' => 'required|string|max:50|unique:vouchers,code',
            'voucherType' => 'required|in:fixed,percent',
            'voucherValue' => 'required|numeric|min:0',
            'voucherExpiry' => 'required|date',
            'voucherLimit' => 'required|integer|min:1',
        ]);

        Voucher::create([
            'code' => strtoupper($this->voucherCode),
            'discount_type' => $this->voucherType,
            'discount_value' => $this->voucherValue,
            'expiry_date' => $this->voucherExpiry,
            'usage_limit' => $this->voucherLimit,
        ]);

        $this->reset(['voucherCode', 'voucherValue', 'voucherExpiry']);
        session()->flash('voucher_message', 'Voucher dibuat.');
    }

    public function createPromo()
    {
        $this->validate([
            'promoCode' => 'required|string|max:50|unique:promos,code',
            'promoType' => 'required|in:fixed,percent',
            'promoValue' => 'required|numeric|min:0',
            'promoExpiry' => 'required|date',
        ]);

        Promo::create([
            'code' => strtoupper($this->promoCode),
            'discount_type' => $this->promoType,
            'discount_value' => $this->promoValue,
            'expiry_date' => $this->promoExpiry,
        ]);

        $this->reset(['promoCode', 'promoValue', 'promoExpiry']);
        session()->flash('promo_message', 'Promo dibuat.');
    }

    public function simulateNextDay()
    {
        $offset = TimeService::simulateNextDay();
        session()->flash('time_message', "Waktu sistem dimajukan. Offset sekarang: {$offset} hari. Waktu simulasi sekarang: ".TimeService::now()->format('d M Y H:i'));
    }

    public function runOverdueCheck()
    {
        $processed = OverdueService::run();
        $this->lastOverdueSummary = $processed->isEmpty()
            ? 'Tidak ada order overdue saat ini.'
            : 'Order diproses (refund/return): #'.$processed->pluck('id')->implode(', #');

        session()->flash('overdue_message', $this->lastOverdueSummary);
    }

    public function render()
    {
        $viewModel = app(AdminDashboardDataService::class)->getViewModel();

        return view('livewire.admin.admin-dashboard', $viewModel);
    }
}
