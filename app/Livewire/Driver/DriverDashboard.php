<?php

namespace App\Livewire\Driver;

use App\Models\DeliveryJob;
use App\Services\DriverDashboardDataService;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class DriverDashboard extends Component
{
    use WithPagination;

    public string $tab = 'available';

    public function setTab(string $tab)
    {
        $this->tab = $tab;
        $this->resetErrorBag();
    }

    public function takeJob(int $jobId)
    {
        $job = DeliveryJob::findOrFail($jobId);

        try {
            OrderService::driverTakeJob(Auth::user(), $job);
            session()->flash('job_message', "Job #{$job->id} berhasil diambil.");
        } catch (\RuntimeException $e) {
            $this->addError('job', $e->getMessage());
        }
    }

    public function completeJob(int $jobId)
    {
        $job = DeliveryJob::findOrFail($jobId);

        try {
            OrderService::driverCompleteJob(Auth::user(), $job);
            session()->flash('job_message', "Job #{$job->id} selesai! Penghasilan sudah masuk ke wallet Anda.");
        } catch (\RuntimeException $e) {
            $this->addError('job', $e->getMessage());
        }
    }

    public function render()
    {
        $viewModel = app(DriverDashboardDataService::class)->getViewModel();

        return view('livewire.driver.driver-dashboard', $viewModel);
    }
}
