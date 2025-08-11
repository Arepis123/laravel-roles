<?php

namespace App\Livewire\Asset;

use App\Models\Asset;
use Livewire\Component;
use Livewire\WithFileUploads;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;

class AssetCreate extends Component
{
    use WithFileUploads;

    public $name = '';
    public $description = '';
    public $asset_type = '';
    public $asset_code = '';
    public $status = 'available';
    public $location = '';
    public $capacity = '';
    public $purchase_date = '';
    public $purchase_price = '';
    public $depreciation_rate = 0;
    public $maintenance_schedule = '';
    public $image;
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
        'asset_code' => 'nullable|string|max:255|unique:assets,asset_code',
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

    public function mount()
    {
        $this->status = 'available';
        $this->is_bookable = true;
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

    public function generateAssetCode()
    {
        if (!$this->asset_type) {
            Flux::toast('Please select asset type first.', variant: 'warning');
            return;
        }

        $this->asset_code = $this->createAssetCode($this->asset_type);
    }

    private function createAssetCode($type)
    {
        $prefix = match($type) {
            'meeting_room' => 'MR',
            'vehicle' => 'VH',
            'it_equipment' => 'IT',
            default => 'AS'
        };

        $lastAsset = Asset::where('asset_code', 'like', $prefix . '%')
            ->orderBy('asset_code', 'desc')
            ->first();

        if ($lastAsset) {
            $lastNumber = (int) substr($lastAsset->asset_code, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'asset_type' => $this->asset_type,
            'asset_code' => $this->asset_code ?: $this->createAssetCode($this->asset_type),
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
            $data['image'] = $this->image->store('assets', 'public');
        }

        $asset = Asset::create($data);

        Flux::toast('Asset created successfully!', variant: 'success');
        return $this->redirect(route('assets.show', $asset->id), navigate: true);
    }

    public function render()
    {
        return view('livewire.assets.asset-create', [
            'assetTypes' => Asset::getTypes(),
            'assetStatuses' => Asset::getStatuses(),
        ]);
    }
}