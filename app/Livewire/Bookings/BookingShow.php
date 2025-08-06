<?php

namespace App\Livewire\Bookings;

use Livewire\Component;
use App\Models\Booking;
use App\Models\MeetingRoom;
use App\Models\Vehicle;
use App\Models\ItAsset;

class BookingShow extends Component
{
    public $booking;
    public string $asset_type = '';
    public string $asset_id = '';
    public string $start_time = '';
    public string $end_time = '';
    public string $purpose = '';
    public string $capacity = '';    

    public array $additional_booking = [];
    public string $refreshment_details = '';    

    public function mount($id)
    {
        $this->booking = Booking::findOrFail($id);

        $this->asset_type = $this->booking->asset_type ?? '';
        $this->asset_id = $this->booking->asset_id ?? '';
        $this->start_time = $this->booking->start_time ?? '';
        $this->end_time = $this->booking->end_time ?? '';
        $this->purpose = $this->booking->purpose ?? '';
        $this->capacity = $this->booking->capacity ?? '';
        $this->additional_booking = $this->booking->additional_booking ?? [];
        $this->refreshment_details = $this->booking->refreshment_details ?? '';
    }

    public function render()
    {
        return view('livewire.bookings.booking-show');
    }

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
}