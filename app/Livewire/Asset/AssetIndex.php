<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithPagination;
use Flux\Flux;

class AssetIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $bookableFilter = '';
    public $showFilters = false;

    public $selectedAssets = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'typeFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'bookableFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingBookableFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAssets = $this->assets->pluck('id')->toArray();
        } else {
            $this->selectedAssets = [];
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->bookableFilter = '';
        $this->resetPage();
    }

    public function deleteAsset($assetId)
    {
        $asset = Asset::find($assetId);
        
        if (!$asset) {
            Flux::toast('Asset not found.', variant: 'danger');
            return;
        }

        // Check if asset has active bookings
        $activeBookings = $asset->bookings()
            ->whereIn('status', ['confirmed', 'pending'])
            ->where('end_time', '>=', now())
            ->count();

        if ($activeBookings > 0) {
            Flux::toast('Cannot delete asset with active bookings.', variant: 'danger');
            return;
        }

        $asset->delete();
        Flux::toast('Asset deleted successfully.', variant: 'success');
    }

    public function bulkDelete()
    {
        if (empty($this->selectedAssets)) {
            Flux::toast('Please select assets to delete.', variant: 'warning');
            return;
        }

        $assets = Asset::whereIn('id', $this->selectedAssets)->get();
        $cannotDelete = [];
        $deleted = 0;

        foreach ($assets as $asset) {
            $activeBookings = $asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('end_time', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                $cannotDelete[] = $asset->name;
            } else {
                $asset->delete();
                $deleted++;
            }
        }

        $this->selectedAssets = [];
        $this->selectAll = false;

        if ($deleted > 0) {
            Flux::toast("$deleted assets deleted successfully.", variant: 'success');
        }

        if (!empty($cannotDelete)) {
            $names = implode(', ', $cannotDelete);
            Flux::toast("Cannot delete assets with active bookings: $names", variant: 'warning');
        }
    }

    public function getAssetsProperty()
    {
        $query = Asset::with(['bookings', 'maintenanceLogs'])
            ->withCount(['bookings as active_bookings_count' => function ($q) {
                $q->whereIn('status', ['confirmed', 'pending'])
                  ->where('end_time', '>=', now());
            }]);

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('asset_code', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        // Apply filters
        if ($this->typeFilter) {
            $query->where('asset_type', $this->typeFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->bookableFilter !== '') {
            $query->where('is_bookable', $this->bookableFilter === '1');
        }

        return $query->latest()->paginate(15);
    }

    public function render()
    {
        return view('livewire.assets.asset-index', [
            'assets' => $this->assets,
            'assetTypes' => Asset::getTypes(),
            'assetStatuses' => Asset::getStatuses(),
        ]);
    }
}