<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Vehicle;
use App\Models\VehicleCheckupLog;
use App\Models\Booking;
use App\Models\CheckupTemplate;
use Carbon\Carbon;

class VehicleCheckupManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $editingCheckup = null;

    // Form fields - Basic
    public $vehicle_id = '';
    public $booking_id = '';
    public $checkup_type = 'pre_trip';
    public $odometer_reading = '';
    public $fuel_level = '';
    public $template_id = null;

    // Exterior Checks
    public $exterior_body_condition = true;
    public $exterior_body_notes = '';
    public $exterior_lights = true;
    public $exterior_lights_notes = '';
    public $exterior_mirrors = true;
    public $exterior_mirrors_notes = '';
    public $exterior_windshield = true;
    public $exterior_windshield_notes = '';
    public $exterior_tires = true;
    public $exterior_tires_notes = '';

    // Interior Checks
    public $interior_seats_seatbelts = true;
    public $interior_seats_seatbelts_notes = '';
    public $interior_dashboard = true;
    public $interior_dashboard_notes = '';
    public $interior_horn = true;
    public $interior_horn_notes = '';
    public $interior_wipers = true;
    public $interior_wipers_notes = '';
    public $interior_ac = true;
    public $interior_ac_notes = '';
    public $interior_cleanliness = true;
    public $interior_cleanliness_notes = '';

    // Engine Checks
    public $engine_oil = true;
    public $engine_oil_notes = '';
    public $engine_coolant = true;
    public $engine_coolant_notes = '';
    public $engine_brake_fluid = true;
    public $engine_brake_fluid_notes = '';
    public $engine_battery = true;
    public $engine_battery_notes = '';
    public $engine_washer_fluid = true;
    public $engine_washer_fluid_notes = '';

    // Functional Checks
    public $functional_brakes = true;
    public $functional_brakes_notes = '';
    public $functional_steering = true;
    public $functional_steering_notes = '';
    public $functional_transmission = true;
    public $functional_transmission_notes = '';
    public $functional_emergency_kit = true;
    public $functional_emergency_kit_notes = '';

    // Overall
    public $overall_status = 'approved';
    public $general_notes = '';
    public $photos = [];

    // Filters
    public $filterVehicle = '';
    public $filterCheckupType = '';
    public $filterStatus = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    // Sorting
    public $sortField = 'checked_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->filterDateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedVehicleId()
    {
        // Auto-populate latest odometer reading if available
        if ($this->vehicle_id) {
            $latestOdometer = \App\Models\VehicleOdometerLog::getLatestReadingForVehicle($this->vehicle_id);
            if ($latestOdometer) {
                $this->odometer_reading = $latestOdometer->odometer_reading;
            }

            // Auto-select template when vehicle changes
            $this->loadDefaultTemplate();
        }
    }

    public function updatedCheckupType()
    {
        // Auto-select template when checkup type changes
        $this->loadDefaultTemplate();
    }

    private function loadDefaultTemplate()
    {
        if ($this->vehicle_id && $this->checkup_type) {
            $vehicle = Vehicle::find($this->vehicle_id);
            if ($vehicle) {
                $template = CheckupTemplate::where('is_active', true)
                    ->defaultFor($vehicle->vehicle_type, $this->checkup_type)
                    ->first();

                if ($template) {
                    $this->template_id = $template->id;
                }
            }
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->editingCheckup = null;
        $this->dispatch('open-modal');
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->editingCheckup = null;
        $this->dispatch('close-modal');
    }

    public function editCheckup($checkupId)
    {
        $checkup = VehicleCheckupLog::findOrFail($checkupId);

        $this->editingCheckup = $checkup->id;
        $this->vehicle_id = $checkup->vehicle_id;
        $this->booking_id = $checkup->booking_id;
        $this->checkup_type = $checkup->checkup_type;
        $this->template_id = $checkup->template_id;
        $this->odometer_reading = $checkup->odometer_reading;
        $this->fuel_level = $checkup->fuel_level ? (int) $checkup->fuel_level : null;

        // Load all check fields
        $this->loadCheckFields($checkup);

        $this->overall_status = $checkup->overall_status;
        $this->general_notes = $checkup->general_notes;

        $this->dispatch('open-modal');
    }

    private function loadCheckFields($checkup)
    {
        // Exterior
        $this->exterior_body_condition = $checkup->exterior_body_condition;
        $this->exterior_body_notes = $checkup->exterior_body_notes;
        $this->exterior_lights = $checkup->exterior_lights;
        $this->exterior_lights_notes = $checkup->exterior_lights_notes;
        $this->exterior_mirrors = $checkup->exterior_mirrors;
        $this->exterior_mirrors_notes = $checkup->exterior_mirrors_notes;
        $this->exterior_windshield = $checkup->exterior_windshield;
        $this->exterior_windshield_notes = $checkup->exterior_windshield_notes;
        $this->exterior_tires = $checkup->exterior_tires;
        $this->exterior_tires_notes = $checkup->exterior_tires_notes;

        // Interior
        $this->interior_seats_seatbelts = $checkup->interior_seats_seatbelts;
        $this->interior_seats_seatbelts_notes = $checkup->interior_seats_seatbelts_notes;
        $this->interior_dashboard = $checkup->interior_dashboard;
        $this->interior_dashboard_notes = $checkup->interior_dashboard_notes;
        $this->interior_horn = $checkup->interior_horn;
        $this->interior_horn_notes = $checkup->interior_horn_notes;
        $this->interior_wipers = $checkup->interior_wipers;
        $this->interior_wipers_notes = $checkup->interior_wipers_notes;
        $this->interior_ac = $checkup->interior_ac;
        $this->interior_ac_notes = $checkup->interior_ac_notes;
        $this->interior_cleanliness = $checkup->interior_cleanliness;
        $this->interior_cleanliness_notes = $checkup->interior_cleanliness_notes;

        // Engine
        $this->engine_oil = $checkup->engine_oil;
        $this->engine_oil_notes = $checkup->engine_oil_notes;
        $this->engine_coolant = $checkup->engine_coolant;
        $this->engine_coolant_notes = $checkup->engine_coolant_notes;
        $this->engine_brake_fluid = $checkup->engine_brake_fluid;
        $this->engine_brake_fluid_notes = $checkup->engine_brake_fluid_notes;
        $this->engine_battery = $checkup->engine_battery;
        $this->engine_battery_notes = $checkup->engine_battery_notes;
        $this->engine_washer_fluid = $checkup->engine_washer_fluid;
        $this->engine_washer_fluid_notes = $checkup->engine_washer_fluid_notes;

        // Functional
        $this->functional_brakes = $checkup->functional_brakes;
        $this->functional_brakes_notes = $checkup->functional_brakes_notes;
        $this->functional_steering = $checkup->functional_steering;
        $this->functional_steering_notes = $checkup->functional_steering_notes;
        $this->functional_transmission = $checkup->functional_transmission;
        $this->functional_transmission_notes = $checkup->functional_transmission_notes;
        $this->functional_emergency_kit = $checkup->functional_emergency_kit;
        $this->functional_emergency_kit_notes = $checkup->functional_emergency_kit_notes;
    }

    public function saveCheckup()
    {
        $this->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'checkup_type' => 'required|in:pre_trip,post_trip,weekly,monthly,annual',
            'odometer_reading' => 'nullable|integer|min:0',
            'fuel_level' => 'nullable|integer|min:1|max:8',
            'overall_status' => 'required|in:approved,approved_with_notes,rejected,needs_maintenance',
        ]);

        $data = $this->getCheckupData();

        if ($this->editingCheckup) {
            $checkup = VehicleCheckupLog::findOrFail($this->editingCheckup);
            $checkup->update($data);
        } else {
            $data['user_id'] = auth()->id();
            $data['checked_at'] = now();
            VehicleCheckupLog::create($data);
        }

        $this->resetForm();
        $this->dispatch('close-modal');
        session()->flash('success', 'Vehicle checkup saved successfully!');
    }

    private function getCheckupData()
    {
        return [
            'vehicle_id' => $this->vehicle_id,
            'booking_id' => $this->booking_id ?: null,
            'template_id' => $this->template_id,
            'checkup_type' => $this->checkup_type,
            'odometer_reading' => $this->odometer_reading ?: null,
            'fuel_level' => $this->fuel_level ?: null,

            // Exterior
            'exterior_body_condition' => $this->exterior_body_condition,
            'exterior_body_notes' => $this->exterior_body_notes,
            'exterior_lights' => $this->exterior_lights,
            'exterior_lights_notes' => $this->exterior_lights_notes,
            'exterior_mirrors' => $this->exterior_mirrors,
            'exterior_mirrors_notes' => $this->exterior_mirrors_notes,
            'exterior_windshield' => $this->exterior_windshield,
            'exterior_windshield_notes' => $this->exterior_windshield_notes,
            'exterior_tires' => $this->exterior_tires,
            'exterior_tires_notes' => $this->exterior_tires_notes,

            // Interior
            'interior_seats_seatbelts' => $this->interior_seats_seatbelts,
            'interior_seats_seatbelts_notes' => $this->interior_seats_seatbelts_notes,
            'interior_dashboard' => $this->interior_dashboard,
            'interior_dashboard_notes' => $this->interior_dashboard_notes,
            'interior_horn' => $this->interior_horn,
            'interior_horn_notes' => $this->interior_horn_notes,
            'interior_wipers' => $this->interior_wipers,
            'interior_wipers_notes' => $this->interior_wipers_notes,
            'interior_ac' => $this->interior_ac,
            'interior_ac_notes' => $this->interior_ac_notes,
            'interior_cleanliness' => $this->interior_cleanliness,
            'interior_cleanliness_notes' => $this->interior_cleanliness_notes,

            // Engine
            'engine_oil' => $this->engine_oil,
            'engine_oil_notes' => $this->engine_oil_notes,
            'engine_coolant' => $this->engine_coolant,
            'engine_coolant_notes' => $this->engine_coolant_notes,
            'engine_brake_fluid' => $this->engine_brake_fluid,
            'engine_brake_fluid_notes' => $this->engine_brake_fluid_notes,
            'engine_battery' => $this->engine_battery,
            'engine_battery_notes' => $this->engine_battery_notes,
            'engine_washer_fluid' => $this->engine_washer_fluid,
            'engine_washer_fluid_notes' => $this->engine_washer_fluid_notes,

            // Functional
            'functional_brakes' => $this->functional_brakes,
            'functional_brakes_notes' => $this->functional_brakes_notes,
            'functional_steering' => $this->functional_steering,
            'functional_steering_notes' => $this->functional_steering_notes,
            'functional_transmission' => $this->functional_transmission,
            'functional_transmission_notes' => $this->functional_transmission_notes,
            'functional_emergency_kit' => $this->functional_emergency_kit,
            'functional_emergency_kit_notes' => $this->functional_emergency_kit_notes,

            // Overall
            'overall_status' => $this->overall_status,
            'general_notes' => $this->general_notes,
        ];
    }

    public function deleteCheckup($checkupId)
    {
        $checkup = VehicleCheckupLog::findOrFail($checkupId);
        $vehicleName = $checkup->vehicle->model ?? 'Unknown';

        $checkup->delete();

        session()->flash('success', "Checkup for {$vehicleName} deleted successfully!");
    }

    public function exportCheckupData($format)
    {
        $this->dispatch('checkup-export', [
            'format' => $format,
            'vehicle' => $this->filterVehicle,
            'checkup_type' => $this->filterCheckupType,
            'status' => $this->filterStatus,
            'date_from' => $this->filterDateFrom,
            'date_to' => $this->filterDateTo,
        ]);
    }

    private function resetForm()
    {
        $this->vehicle_id = '';
        $this->booking_id = '';
        $this->checkup_type = 'pre_trip';
        $this->template_id = null;
        $this->odometer_reading = '';
        $this->fuel_level = '';
        $this->resetAllChecks();
        $this->overall_status = 'approved';
        $this->general_notes = '';
        $this->photos = [];
        $this->resetErrorBag();
    }

    private function resetAllChecks()
    {
        // Reset all check fields to true (passing)
        $checks = [
            'exterior_body_condition', 'exterior_lights', 'exterior_mirrors',
            'exterior_windshield', 'exterior_tires',
            'interior_seats_seatbelts', 'interior_dashboard', 'interior_horn',
            'interior_wipers', 'interior_ac', 'interior_cleanliness',
            'engine_oil', 'engine_coolant', 'engine_brake_fluid', 'engine_battery', 'engine_washer_fluid',
            'functional_brakes', 'functional_steering', 'functional_transmission',
            'functional_emergency_kit'
        ];

        foreach ($checks as $check) {
            $this->$check = true;
            $this->{$check . '_notes'} = '';
        }
    }

    public function getVehiclesProperty()
    {
        return Vehicle::orderBy('model')->get();
    }

    public function getTemplatesProperty()
    {
        return CheckupTemplate::where('is_active', true)->orderBy('name')->get();
    }

    public function getCurrentTemplateProperty()
    {
        if ($this->template_id) {
            return CheckupTemplate::find($this->template_id);
        }
        return null;
    }

    public function isCheckApplicable($checkField)
    {
        // If no template selected, show all checks
        if (!$this->currentTemplate) {
            return true;
        }

        return $this->currentTemplate->isCheckApplicable($checkField);
    }

    public function getAllAvailableChecksProperty()
    {
        return CheckupTemplate::getAllAvailableChecks();
    }

    public function getCheckupLogsProperty()
    {
        $query = VehicleCheckupLog::with(['vehicle', 'user', 'booking']);

        // Apply filters
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterCheckupType) {
            $query->where('checkup_type', $this->filterCheckupType);
        }

        if ($this->filterStatus) {
            $query->where('overall_status', $this->filterStatus);
        }

        if ($this->filterDateFrom) {
            $query->where('checked_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('checked_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = VehicleCheckupLog::query();

        // Apply same filters as main query
        if ($this->filterVehicle) {
            $query->where('vehicle_id', $this->filterVehicle);
        }

        if ($this->filterDateFrom) {
            $query->where('checked_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->where('checked_at', '<=', $this->filterDateTo . ' 23:59:59');
        }

        $logs = $query->get();

        return [
            'total_checkups' => $logs->count(),
            'approved' => $logs->where('overall_status', 'approved')->count(),
            'approved_with_notes' => $logs->where('overall_status', 'approved_with_notes')->count(),
            'rejected' => $logs->where('overall_status', 'rejected')->count(),
            'needs_maintenance' => $logs->where('overall_status', 'needs_maintenance')->count(),
            'vehicles_checked' => $logs->pluck('vehicle_id')->unique()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.vehicle-checkup-management', [
            'checkupLogs' => $this->checkupLogs,
            'vehicles' => $this->vehicles,
            'templates' => $this->templates,
            'currentTemplate' => $this->currentTemplate,
            'allAvailableChecks' => $this->allAvailableChecks,
            'stats' => $this->stats
        ]);
    }
}
