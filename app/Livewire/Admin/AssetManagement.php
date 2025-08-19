<?php

namespace App\Livewire\Admin;

use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use App\Models\Booking;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;

class AssetManagement extends Component
{
    use WithPagination;

    public $selectedAssetType = 'all';
    public $search = '';
    public $showModal = false;
    public $editingAsset = null;
    public $assetType = '';
    
    // Meeting Room specific fields
    #[Validate('required|string|max:255')]
    public $meeting_room_name = '';
    
    #[Validate('nullable|string|max:255')]
    public $meeting_room_location = '';
    
    #[Validate('nullable|integer|min:1')]
    public $meeting_room_capacity = null;
    
    #[Validate('nullable|boolean')]
    public $has_projector = false;
    
    #[Validate('nullable|string')]
    public $meeting_room_notes = '';
    
    // Vehicle specific fields
    #[Validate('nullable|string|max:255')]
    public $vehicle_model = '';
    
    #[Validate('nullable|string|max:20')]
    public $plate_number = '';
    
    #[Validate('nullable|integer|min:1')]
    public $vehicle_capacity = null;
    
    #[Validate('nullable|string|max:255')]
    public $driver_name = '';
    
    #[Validate('nullable|string')]
    public $vehicle_notes = '';
    
    // IT Asset specific fields
    #[Validate('nullable|string|max:255')]
    public $it_asset_name = '';
    
    #[Validate('nullable|string|max:255')]
    public $asset_tag = '';
    
    #[Validate('nullable|string|max:255')]
    public $it_asset_location = '';
    
    #[Validate('nullable|string')]
    public $specs = '';
    
    #[Validate('nullable|string')]
    public $it_asset_notes = '';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedAssetType()
    {
        $this->resetPage();
    }

    public function getAllAssets()
    {
        $assets = collect();
        
        // Get Meeting Rooms
        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'meeting_rooms') {
            $meetingRooms = MeetingRoom::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
            })->get()->map(function($room) {
                return [
                    'id' => $room->id,
                    'type' => 'meeting_room',
                    'type_label' => 'Meeting Room',
                    'name' => $room->name,
                    'details' => $room->location . ($room->capacity ? " (Cap: {$room->capacity})" : ''),
                    'status' => $this->getAssetStatus('App\Models\MeetingRoom', $room->id),
                    'bookings_count' => $room->bookings()->count(),
                    'model' => $room
                ];
            });
            $assets = $assets->merge($meetingRooms);
        }

        // Get Vehicles
        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'vehicles') {
            $vehicles = Vehicle::when($this->search, function($query) {
                $query->where('model', 'like', '%' . $this->search . '%')
                      ->orWhere('plate_number', 'like', '%' . $this->search . '%')
                      ->orWhere('driver_name', 'like', '%' . $this->search . '%');
            })->get()->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'type' => 'vehicle',
                    'type_label' => 'Vehicle',
                    'name' => $vehicle->model,
                    'details' => ($vehicle->driver_name ? "Driver: {$vehicle->driver_name}" : 'No driver assigned') . ($vehicle->capacity ? " (Cap: {$vehicle->capacity})" : ''),
                    'status' => $this->getAssetStatus('App\Models\Vehicle', $vehicle->id),
                    'bookings_count' => $vehicle->bookings()->count(),
                    'model' => $vehicle
                ];
            });
            $assets = $assets->merge($vehicles);
        }

        // Get IT Assets
        if ($this->selectedAssetType === 'all' || $this->selectedAssetType === 'it_assets') {
            $itAssets = ItAsset::when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('asset_tag', 'like', '%' . $this->search . '%')
                      ->orWhere('location', 'like', '%' . $this->search . '%');
            })->get()->map(function($asset) {
                return [
                    'id' => $asset->id,
                    'type' => 'it_asset',
                    'type_label' => 'IT Asset',
                    'name' => $asset->name . ($asset->asset_tag ? " ({$asset->asset_tag})" : ''),
                    'details' => $asset->location . ($asset->specs ? " - {$asset->specs}" : ''),
                    'status' => $this->getAssetStatus('App\Models\ItAsset', $asset->id),
                    'bookings_count' => $asset->bookings()->count(),
                    'model' => $asset
                ];
            });
            $assets = $assets->merge($itAssets);
        }

        return $assets->sortBy('name');
    }

    private function getAssetStatus($modelType, $assetId)
    {
        $activeBooking = Booking::where('asset_type', $modelType)
            ->where('asset_id', $assetId)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->where('status', 'approved')
            ->first();

        return $activeBooking ? 'In Use' : 'Available';
    }

    public function createAsset($type)
    {
        // Check permission for creating assets
        if (!auth()->user()->hasPermissionTo('asset.create')) {
            session()->flash('error', 'You do not have permission to create assets.');
            return;
        }

        $this->resetForm();
        $this->assetType = $type;
        $this->showModal = true;
    }

    public function editAsset($type, $id)
    {
        // Check permission for editing assets
        if (!auth()->user()->hasPermissionTo('asset.edit')) {
            session()->flash('error', 'You do not have permission to edit assets.');
            return;
        }

        $this->resetForm();
        $this->assetType = $type;
        
        switch($type) {
            case 'meeting_room':
                $asset = MeetingRoom::find($id);
                $this->meeting_room_name = $asset->name;
                $this->meeting_room_location = $asset->location;
                $this->meeting_room_capacity = $asset->capacity;
                $this->has_projector = $asset->has_projector;
                $this->meeting_room_notes = $asset->notes;
                break;
                
            case 'vehicle':
                $asset = Vehicle::find($id);
                $this->vehicle_model = $asset->model;
                $this->plate_number = $asset->plate_number;
                $this->vehicle_capacity = $asset->capacity;
                $this->driver_name = $asset->driver_name;
                $this->vehicle_notes = $asset->notes;
                break;
                
            case 'it_asset':
                $asset = ItAsset::find($id);
                $this->it_asset_name = $asset->name;
                $this->asset_tag = $asset->asset_tag;
                $this->it_asset_location = $asset->location;
                $this->specs = $asset->specs;
                $this->it_asset_notes = $asset->notes;
                break;
        }
        
        $this->editingAsset = $id;
        $this->showModal = true;
    }

    public function saveAsset()
    {
        // Check permission for creating/editing assets
        $permission = $this->editingAsset ? 'asset.edit' : 'asset.create';
        if (!auth()->user()->hasPermissionTo($permission)) {
            session()->flash('error', "You do not have permission to {$permission}.");
            return;
        }

        $this->validate($this->getValidationRules());
        
        try {
            if ($this->editingAsset) {
                $this->updateAsset();
            } else {
                $this->createNewAsset();
            }
            
            $this->showModal = false;
            $this->resetForm();
            session()->flash('success', 'Asset saved successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving asset: ' . $e->getMessage());
        }
    }

    private function createNewAsset()
    {
        switch($this->assetType) {
            case 'meeting_room':
                MeetingRoom::create([
                    'name' => $this->meeting_room_name,
                    'location' => $this->meeting_room_location,
                    'capacity' => $this->meeting_room_capacity,
                    'has_projector' => $this->has_projector,
                    'notes' => $this->meeting_room_notes,
                ]);
                break;
                
            case 'vehicle':
                Vehicle::create([
                    'model' => $this->vehicle_model,
                    'plate_number' => $this->plate_number,
                    'capacity' => $this->vehicle_capacity,
                    'driver_name' => $this->driver_name,
                    'notes' => $this->vehicle_notes,
                ]);
                break;
                
            case 'it_asset':
                ItAsset::create([
                    'name' => $this->it_asset_name,
                    'asset_tag' => $this->asset_tag,
                    'location' => $this->it_asset_location,
                    'specs' => $this->specs,
                    'notes' => $this->it_asset_notes,
                ]);
                break;
        }
    }

    private function updateAsset()
    {
        switch($this->assetType) {
            case 'meeting_room':
                MeetingRoom::find($this->editingAsset)->update([
                    'name' => $this->meeting_room_name,
                    'location' => $this->meeting_room_location,
                    'capacity' => $this->meeting_room_capacity,
                    'has_projector' => $this->has_projector,
                    'notes' => $this->meeting_room_notes,
                ]);
                break;
                
            case 'vehicle':
                Vehicle::find($this->editingAsset)->update([
                    'model' => $this->vehicle_model,
                    'plate_number' => $this->plate_number,
                    'capacity' => $this->vehicle_capacity,
                    'driver_name' => $this->driver_name,
                    'notes' => $this->vehicle_notes,
                ]);
                break;
                
            case 'it_asset':
                ItAsset::find($this->editingAsset)->update([
                    'name' => $this->it_asset_name,
                    'asset_tag' => $this->asset_tag,
                    'location' => $this->it_asset_location,
                    'specs' => $this->specs,
                    'notes' => $this->it_asset_notes,
                ]);
                break;
        }
    }

    public function deleteAsset($type, $id)
    {
        // Check permission for deleting assets
        if (!auth()->user()->hasPermissionTo('delete assets')) {
            session()->flash('error', 'You do not have permission to delete assets.');
            return;
        }

        try {
            // Check if asset has active bookings
            $modelClass = $this->getModelClass($type);
            $activeBookings = Booking::where('asset_type', $modelClass)
                ->where('asset_id', $id)
                ->where('end_time', '>=', now())
                ->where('status', '!=', 'cancelled')
                ->count();

            if ($activeBookings > 0) {
                session()->flash('error', 'Cannot delete asset with active or future bookings.');
                return;
            }

            switch($type) {
                case 'meeting_room':
                    MeetingRoom::find($id)->delete();
                    break;
                case 'vehicle':
                    Vehicle::find($id)->delete();
                    break;
                case 'it_asset':
                    ItAsset::find($id)->delete();
                    break;
            }
            
            session()->flash('success', 'Asset deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting asset: ' . $e->getMessage());
        }
    }

    private function getModelClass($type)
    {
        switch($type) {
            case 'meeting_room':
                return 'App\Models\MeetingRoom';
            case 'vehicle':
                return 'App\Models\Vehicle';
            case 'it_asset':
                return 'App\Models\ItAsset';
        }
    }

    private function getValidationRules()
    {
        $rules = [];
        
        switch($this->assetType) {
            case 'meeting_room':
                $rules = [
                    'meeting_room_name' => 'required|string|max:255',
                    'meeting_room_location' => 'nullable|string|max:255',
                    'meeting_room_capacity' => 'nullable|integer|min:1',
                    'has_projector' => 'nullable|boolean',
                    'meeting_room_notes' => 'nullable|string',
                ];
                break;
                
            case 'vehicle':
                $rules = [
                    'vehicle_model' => 'required|string|max:255',
                    'plate_number' => 'required|string|max:20|unique:vehicles,plate_number' . ($this->editingAsset ? ',' . $this->editingAsset : ''),
                    'vehicle_capacity' => 'nullable|integer|min:1',
                    'driver_name' => 'nullable|string|max:255',
                    'vehicle_notes' => 'nullable|string',
                ];
                break;
                
            case 'it_asset':
                $rules = [
                    'it_asset_name' => 'required|string|max:255',
                    'asset_tag' => 'nullable|string|max:255|unique:it_assets,asset_tag' . ($this->editingAsset ? ',' . $this->editingAsset : ''),
                    'it_asset_location' => 'nullable|string|max:255',
                    'specs' => 'nullable|string',
                    'it_asset_notes' => 'nullable|string',
                ];
                break;
        }
        
        return $rules;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        // Meeting Room fields
        $this->meeting_room_name = '';
        $this->meeting_room_location = '';
        $this->meeting_room_capacity = null;
        $this->has_projector = false;
        $this->meeting_room_notes = '';
        
        // Vehicle fields
        $this->vehicle_model = '';
        $this->plate_number = '';
        $this->vehicle_capacity = null;
        $this->driver_name = '';
        $this->vehicle_notes = '';
        
        // IT Asset fields
        $this->it_asset_name = '';
        $this->asset_tag = '';
        $this->it_asset_location = '';
        $this->specs = '';
        $this->it_asset_notes = '';
        
        $this->editingAsset = null;
        $this->assetType = '';
    }

    public function getAssetStats()
    {
        return [
            'meeting_rooms' => MeetingRoom::count(),
            'vehicles' => Vehicle::count(),
            'it_assets' => ItAsset::count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->where('status', 'approved')
                ->count(),
        ];
    }

    public function render()
    {
        return view('livewire.admin.asset-management', [
            'assets' => $this->getAllAssets(),
            'stats' => $this->getAssetStats(),
        ]);
    }
}