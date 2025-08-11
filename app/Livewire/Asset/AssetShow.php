<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use App\Models\MaintenanceLog;
use Livewire\Component;
use Flux\Flux;

class AssetShow extends Component
{
    public Asset $asset;
    
    // Quick maintenance form
    public $showMaintenanceForm = false;
    public $maintenance_type = 'routine';
    public $maintenance_description = '';
    public $maintenance_cost = '';
    public $scheduled_date = '';

    // Quick status update
    public $showStatusForm = false;
    public $newStatus = '';

    protected $listeners = ['refreshAsset' => '$refresh'];

    public function mount(Asset $asset)
    {
        $this->asset = $asset->load([
            'bookings' => function ($q) {
                $q->with('user')->latest();
            },
            'maintenanceLogs' => function ($q) {
                $q->with('user')->latest();
            }
        ]);
    }

    public function toggleMaintenanceForm()
    {
        $this->showMaintenanceForm = !$this->showMaintenanceForm;
        $this->resetMaintenanceForm();
    }

    public function resetMaintenanceForm()
    {
        $this->maintenance_type = 'routine';
        $this->maintenance_description = '';
        $this->maintenance_cost = '';
        $this->scheduled_date = '';
    }

    public function scheduleMaintenance()
    {
        $this->validate([
            'maintenance_type' => 'required|in:routine,preventive,corrective,emergency',
            'maintenance_description' => 'required|string|max:1000',
            'maintenance_cost' => 'nullable|numeric|min:0',
            'scheduled_date' => 'required|date|after_or_equal:today',
        ]);

        MaintenanceLog::create([
            'asset_id' => $this->asset->id,
            'user_id' => auth()->id(),
            'maintenance_type' => $this->maintenance_type,
            'description' => $this->maintenance_description,
            'cost' => $this->maintenance_cost ? (float) $this->maintenance_cost : null,
            'scheduled_date' => $this->scheduled_date,
            'status' => 'scheduled',
        ]);

        $this->showMaintenanceForm = false;
        $this->resetMaintenanceForm();
        $this->asset->refresh();

        Flux::toast('Maintenance scheduled successfully!', variant: 'success');
    }

    public function toggleStatusForm()
    {
        $this->showStatusForm = !$this->showStatusForm;
        $this->newStatus = $this->asset->status;
    }

    public function updateStatus()
    {
        $this->validate([
            'newStatus' => 'required|in:available,in_use,maintenance,damaged,retired',
        ]);

        // Check if trying to set to available but has active bookings
        if ($this->newStatus === 'available') {
            $activeBookings = $this->asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                Flux::toast('Cannot set to available - asset has active bookings.', variant: 'warning');
                return;
            }
        }

        $oldStatus = $this->asset->status;
        $this->asset->update(['status' => $this->newStatus]);
        $this->showStatusForm = false;

        Flux::toast("Asset status updated from {$this->asset->getStatuses()[$oldStatus]} to {$this->asset->getStatuses()[$this->newStatus]}.", variant: 'success');
    }

    public function toggleBookable()
    {
        // Check if has active bookings when trying to disable bookable
        if ($this->asset->is_bookable) {
            $activeBookings = $this->asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('end_time', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                Flux::toast('Cannot disable bookable status - asset has active bookings.', variant: 'warning');
                return;
            }
        }

        $this->asset->update(['is_bookable' => !$this->asset->is_bookable]);
        
        $status = $this->asset->is_bookable ? 'enabled' : 'disabled';
        Flux::toast("Bookable status $status.", variant: 'success');
    }

    public function deleteAsset()
    {
        // Check if asset has active bookings
        $activeBookings = $this->asset->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('end_time', '>=', now())
            ->count();

        if ($activeBookings > 0) {
            Flux::toast('Cannot delete asset with active bookings.', variant: 'danger');
            return;
        }

        $this->asset->delete();
        
        Flux::toast('Asset deleted successfully.', variant: 'success');
        return $this->redirect(route('assets.index'), navigate: true);
    }

    public function getUpcomingBookingsProperty()
    {
        return $this->asset->bookings()
            ->with('user')
            ->where('start_time', '>=', now())
            ->whereIn('status', ['confirmed', 'pending'])
            ->orderBy('start_time')
            ->limit(5)
            ->get();
    }

    public function getRecentMaintenanceProperty()
    {
        return $this->asset->maintenanceLogs()
            ->with('user')
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getAssetStatsProperty()
    {
        return [
            'total_bookings' => $this->asset->bookings()->count(),
            'active_bookings' => $this->asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('end_time', '>=', now())
                ->count(),
            'total_maintenance' => $this->asset->maintenanceLogs()->count(),
            'pending_maintenance' => $this->asset->maintenanceLogs()
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->count(),
            'current_value' => $this->asset->current_value,
        ];
    }

    public function render()
    {
        return view('livewire.assets.asset-show', [
            'upcomingBookings' => $this->upcomingBookings,
            'recentMaintenance' => $this->recentMaintenance,
            'stats' => $this->assetStats,
            'maintenanceTypes' => MaintenanceLog::getTypes(),
            'assetStatuses' => Asset::getStatuses(),
        ]);
    }
}