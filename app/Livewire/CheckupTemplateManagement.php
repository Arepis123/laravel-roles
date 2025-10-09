<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CheckupTemplate;

class CheckupTemplateManagement extends Component
{
    use WithPagination;

    public $editingTemplate = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $vehicle_type = 'all';
    public $checkup_type = 'all';
    public $applicable_checks = [];
    public $is_default = false;
    public $is_active = true;

    // Filters
    public $filterVehicleType = '';
    public $filterCheckupType = '';
    public $filterActive = '';

    // Sorting
    public $sortField = 'name';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function showAddForm()
    {
        $this->resetForm();
        $this->editingTemplate = null;
        $this->dispatch('open-modal');
    }

    public function cancelForm()
    {
        $this->resetForm();
        $this->editingTemplate = null;
        $this->dispatch('close-modal');
    }

    public function editTemplate($templateId)
    {
        $template = CheckupTemplate::findOrFail($templateId);

        $this->editingTemplate = $template->id;
        $this->name = $template->name;
        $this->description = $template->description;
        $this->vehicle_type = $template->vehicle_type;
        $this->checkup_type = $template->checkup_type;
        $this->applicable_checks = $template->applicable_checks ?? [];
        $this->is_default = $template->is_default;
        $this->is_active = $template->is_active;

        $this->dispatch('open-modal');
    }

    public function saveTemplate()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'vehicle_type' => 'required|in:car,motorcycle,van,truck,all',
            'checkup_type' => 'required|in:pre_trip,post_trip,weekly,monthly,annual,all',
            'applicable_checks' => 'required|array|min:1',
            'applicable_checks.*' => 'string|in:' . implode(',', CheckupTemplate::getAllCheckFields()),
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'vehicle_type' => $this->vehicle_type,
            'checkup_type' => $this->checkup_type,
            'applicable_checks' => $this->applicable_checks,
            'is_default' => $this->is_default,
            'is_active' => $this->is_active,
        ];

        if ($this->editingTemplate) {
            $template = CheckupTemplate::findOrFail($this->editingTemplate);

            // If setting as default, unset other defaults for same vehicle/checkup type
            if ($this->is_default && !$template->is_default) {
                $this->unsetOtherDefaults($this->vehicle_type, $this->checkup_type, $template->id);
            }

            $template->update($data);
            session()->flash('success', 'Template updated successfully!');
        } else {
            // If setting as default, unset other defaults for same vehicle/checkup type
            if ($this->is_default) {
                $this->unsetOtherDefaults($this->vehicle_type, $this->checkup_type);
            }

            CheckupTemplate::create($data);
            session()->flash('success', 'Template created successfully!');
        }

        $this->resetForm();
        $this->dispatch('close-modal');
    }

    public function deleteTemplate($templateId)
    {
        $template = CheckupTemplate::findOrFail($templateId);

        // Check if template is being used
        if ($template->checkupLogs()->count() > 0) {
            session()->flash('error', 'Cannot delete template that is being used by checkup logs!');
            return;
        }

        $templateName = $template->name;
        $template->delete();

        session()->flash('success', "Template '{$templateName}' deleted successfully!");
    }

    public function toggleDefault($templateId)
    {
        $template = CheckupTemplate::findOrFail($templateId);

        if (!$template->is_default) {
            // Unset other defaults for same vehicle/checkup type
            $this->unsetOtherDefaults($template->vehicle_type, $template->checkup_type, $template->id);

            $template->update(['is_default' => true]);
            session()->flash('success', 'Template set as default!');
        } else {
            $template->update(['is_default' => false]);
            session()->flash('success', 'Template removed from default!');
        }
    }

    public function toggleActive($templateId)
    {
        $template = CheckupTemplate::findOrFail($templateId);
        $template->update(['is_active' => !$template->is_active]);

        $status = $template->is_active ? 'activated' : 'deactivated';
        session()->flash('success', "Template {$status} successfully!");
    }

    private function unsetOtherDefaults($vehicleType, $checkupType, $excludeId = null)
    {
        $query = CheckupTemplate::where('is_default', true)
            ->where(function($q) use ($vehicleType) {
                $q->where('vehicle_type', $vehicleType)
                  ->orWhere('vehicle_type', 'all');
            })
            ->where(function($q) use ($checkupType) {
                $q->where('checkup_type', $checkupType)
                  ->orWhere('checkup_type', 'all');
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_default' => false]);
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->vehicle_type = 'all';
        $this->checkup_type = 'all';
        $this->applicable_checks = [];
        $this->is_default = false;
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function getTemplatesProperty()
    {
        $query = CheckupTemplate::query();

        // Apply filters
        if ($this->filterVehicleType) {
            $query->where('vehicle_type', $this->filterVehicleType);
        }

        if ($this->filterCheckupType) {
            $query->where('checkup_type', $this->filterCheckupType);
        }

        if ($this->filterActive !== '') {
            $query->where('is_active', $this->filterActive);
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatsProperty()
    {
        $query = CheckupTemplate::query();

        // Apply same filters as main query
        if ($this->filterVehicleType) {
            $query->where('vehicle_type', $this->filterVehicleType);
        }

        if ($this->filterCheckupType) {
            $query->where('checkup_type', $this->filterCheckupType);
        }

        if ($this->filterActive !== '') {
            $query->where('is_active', $this->filterActive);
        }

        $templates = $query->get();

        return [
            'total_templates' => $templates->count(),
            'active_templates' => $templates->where('is_active', true)->count(),
            'default_templates' => $templates->where('is_default', true)->count(),
            'inactive_templates' => $templates->where('is_active', false)->count(),
        ];
    }

    public function getAllAvailableChecksProperty()
    {
        return CheckupTemplate::getAllAvailableChecks();
    }

    public function render()
    {
        return view('livewire.checkup-template-management', [
            'templates' => $this->templates,
            'stats' => $this->stats,
            'allAvailableChecks' => $this->allAvailableChecks,
        ]);
    }
}
