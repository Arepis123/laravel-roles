<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithFileUploads;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;

class AssetEdit extends Component
{
    use WithFileUploads;

    public Asset $asset;

    public $name = '';
    public $description = '';
    public $asset_type = '';
    public $asset_code = '';
    public $status = '';
    public $location = '';
    public $capacity = '';
    public $purchase_date = '';
    public $purchase_price = '';
    public $depreciation_rate = '';
    public $maintenance_schedule = '';
    public $image;
    public $existingImage = '';
    public $is_bookable = true;

    // Specifications (dynamic fields)
    public $specifications = [];
    public $newSpecKey = '';
    public $newSpecValue = '';

    // Booking rules (dynamic fields)
    public $booking_rules = [];
    public $newRuleKey = '';
    public $newRuleValue = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'asset_type' => 'required|in:meeting_room,vehicle,it_equipment,other',
        'asset_code' => 'required|string|max:255',
        'status' => 'required|in:available,in_use,maintenance,damaged,retired',
        'location' => 'nullable|string|max:255',
        'capacity' => 'nullable|integer|min:1',
        'purchase_date' => 'nullable|date|before_or_equal:today',
        'purchase_price' => 'nullable|numeric|min:0',
        'depreciation_rate' => 'nullable|numeric|min:0|max:100',
        'maintenance_schedule' => 'nullable|string|max:255',
        'image' => 'nullable|image|max:2048',
        'is_bookable' => 'boolean',
    ];

    public function mount(Asset $asset)
    {
        $this->asset = $asset;
        $this->fill([
            'name' => $asset->name,
            'description' => $asset->description,
            'asset_type' => $asset->asset_type,
            'asset_code' => $asset->asset_code,
            'status' => $asset->status,
            'location' => $asset->location,
            'capacity' => $asset->capacity,
            'purchase_date' => $asset->purchase_date?->format('Y-m-d'),
            'purchase_price' => $asset->purchase_price,
            'depreciation_rate' => $asset->depreciation_rate,
            'maintenance_schedule' => $asset->maintenance_schedule,
            'existingImage' => $asset->image,
            'is_bookable' => $asset->is_bookable,
            'specifications' => $asset->specifications ?? [],
            'booking_rules' => $asset->booking_rules ?? [],
        ]);
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'asset_type' => 'required|in:meeting_room,vehicle,it_equipment,other',
            'asset_code' => 'required|string|max:255|unique:assets,asset_code,' . $this->asset->id,
            'status' => 'required|in:available,in_use,maintenance,damaged,retired',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'purchase_date' => 'nullable|date|before_or_equal:today',
            'purchase_price' => 'nullable|numeric|min:0',
            'depreciation_rate' => 'nullable|numeric|min:0|max:100',
            'maintenance_schedule' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
            'is_bookable' => 'boolean',
        ];

        return $rules;
    }

    public function addSpecification()
    {
        if ($this->newSpecKey && $this->newSpecValue) {
            $this->specifications[$this->newSpecKey] = $this->newSpecValue;
            $this->newSpecKey = '';
            $this->newSpecValue = '';
        }
    }

    public function removeSpecification($key)
    {
        unset($this->specifications[$key]);
    }

    public function addBookingRule()
    {
        if ($this->newRuleKey && $this->newRuleValue) {
            $this->booking_rules[$this->newRuleKey] = $this->newRuleValue;
            $this->newRuleKey = '';
            $this->newRuleValue = '';
        }
    }

    public function removeBookingRule($key)
    {
        unset($this->booking_rules[$key]);
    }

    public function removeExistingImage()
    {
        if ($this->existingImage) {
            Storage::disk('public')->delete($this->existingImage);
            $this->asset->update(['image' => null]);
            $this->existingImage = '';
            Flux::toast('Image removed successfully.', variant: 'success');
        }
    }

    public function save()
    {
        // Validate status change restrictions
        if ($this->status !== $this->asset->status) {
            $this->validateStatusChange();
        }

        // Validate bookable change restrictions
        if ($this->is_bookable !== $this->asset->is_bookable) {
            $this->validateBookableChange();
        }

        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'asset_type' => $this->asset_type,
            'asset_code' => $this->asset_code,
            'status' => $this->status,
            'location' => $this->location,
            'capacity' => $this->capacity ? (int) $this->capacity : null,
            'purchase_date' => $this->purchase_date ?: null,
            'purchase_price' => $this->purchase_price ? (float) $this->purchase_price : null,
            'depreciation_rate' => (float) $this->depreciation_rate,
            'maintenance_schedule' => $this->maintenance_schedule,
            'is_bookable' => $this->is_bookable,
            'specifications' => $this->specifications,
            'booking_rules' => $this->booking_rules,
        ];

        // Handle image upload
        if ($this->image) {
            // Delete old image
            if ($this->existingImage) {
                Storage::disk('public')->delete($this->existingImage);
            }
            $data['image'] = $this->image->store('assets', 'public');
        }

        $this->asset->update($data);

        Flux::toast('Asset updated successfully!', variant: 'success');
        return $this->redirect(route('assets.show', $this->asset->id), navigate: true);
    }

    private function validateStatusChange()
    {
        // If trying to set to available but has active bookings
        if ($this->status === 'available') {
            $activeBookings = $this->asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('start_time', '<=', now())
                ->where('end_time', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                $this->addError('status', 'Cannot set to available - asset has active bookings.');
            }
        }
    }

    private function validateBookableChange()
    {
        // If trying to disable bookable but has active bookings
        if (!$this->is_bookable) {
            $activeBookings = $this->asset->bookings()
                ->whereIn('status', ['confirmed', 'pending'])
                ->where('end_time', '>=', now())
                ->count();

            if ($activeBookings > 0) {
                $this->addError('is_bookable', 'Cannot disable bookable status - asset has active bookings.');
            }
        }
    }

    public function render()
    {
        return view('livewire.assets.asset-edit', [
            'assetTypes' => Asset::getTypes(),
            'assetStatuses' => Asset::getStatuses(),
        ]);
    }
}