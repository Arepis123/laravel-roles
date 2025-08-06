<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;
use Illuminate\Support\Facades\Auth;

class BookingCreate extends Component
{
    public string $asset_type = '';
    public string $asset_id = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $purpose = '';
    public string $capacity = '';

    public array $additional_booking = [];
    public string $refreshment_details = '';

    protected $assetTypeConfig = [
        'meeting_room' => [
            'label' => 'Meeting Room',
            'model' => MeetingRoom::class,
            'name_field' => 'name'
        ],
        'vehicle' => [
            'label' => 'Vehicle',
            'model' => Vehicle::class,
            'name_field' => 'model'
        ],
        'it_asset' => [
            'label' => 'IT Asset',
            'model' => ItAsset::class,
            'name_field' => 'name'
        ],
    ];

    public function getAssetTypeOptionsProperty()
    {
        return collect($this->assetTypeConfig)->map(function ($config, $key) {
            return [
                'value' => $key,
                'label' => $config['label']
            ];
        });
    }

    public function getAssetOptionsProperty()
    {
        if (empty($this->asset_type) || !isset($this->assetTypeConfig[$this->asset_type])) {
            return collect();
        }

        $config = $this->assetTypeConfig[$this->asset_type];
        $model = $config['model'];
        $nameField = $config['name_field'];

        return $model::select('id', "{$nameField} as name")->get();
    }

    public function updatedAssetType()
    {
        $this->asset_id = '';
    }

    // public function save()
    // {
    //     $this->validate([
    //         'asset_type' => 'required|string',
    //         'asset_id' => 'required|string',
    //         'start_time' => 'required|date',
    //         'end_time' => 'required|date|after:start_time',
    //         'purpose' => 'required|string',
    //         'additional_booking' => 'array',
    //         'refreshment_details' => 'nullable|string|max:255',
    //     ]);

    //     $modelClass = $this->assetTypeConfig[$this->asset_type]['model'] ?? null;

    //     if (!$modelClass) {
    //         session()->flash('error', 'Invalid asset type selected.');
    //         return;
    //     }

    //     auth()->user()->bookings()->create([
    //         'asset_type' => $modelClass,
    //         'asset_id' => $this->asset_id,
    //         'purpose' => $this->purpose,
    //         'start_time' => $this->start_time,
    //         'end_time' => $this->end_time,
    //         'status' => 'pending',
    //         // optional: save additional_booking and refreshment_details if needed
    //         // 'additional_booking' => json_encode($this->additional_booking),
    //         // 'refreshment_details' => $this->refreshment_details,
    //     ]);

    //     session()->flash('success', 'Booking submitted successfully.');
    //     return redirect()->route('bookings.index');
    // }

    public function save()
    {
        $rules = [
            'asset_type' => 'required|string',
            'asset_id' => 'required|string',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'required|string',
            'capacity' => 'numeric',
            'additional_booking' => 'array',
        ];

        // Add conditional rule for refreshment_details
        if (in_array('refreshment', $this->additional_booking)) {
            $rules['refreshment_details'] = 'required|string|min:3';
        }

        $this->validate($rules);

        auth()->user()->bookings()->create([
            'asset_type' => $this->assetTypeConfig[$this->asset_type]['model'] ?? null,
            'asset_id' => $this->asset_id,
            'purpose' => $this->purpose,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'status' => 'pending',
            'capacity' => $this->capacity,
            'additional_booking' => $this->additional_booking,
            'refreshment_details' => in_array('refreshment', $this->additional_booking) ? $this->refreshment_details: null,
        ]);

        session()->flash('success', 'Booking submitted successfully.');
        return redirect()->route('bookings.index');
    }

    public function render()
    {
        return view('livewire.bookings.booking-create');
    }
}
