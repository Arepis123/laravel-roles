<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BookingReminderSetting;
use App\Models\BookingReminderLog;

class ReminderSettings extends Component
{
    use WithPagination;

    public $settings;
    public $enabled;
    public $hours_after_end;
    public $frequency;
    public $send_to_passengers;
    public $skip_weekends;
    public $max_reminders;
    public $custom_message;
    public $excluded_asset_types = [];

    public $showTestModal = false;
    public $testResult = '';

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->settings = BookingReminderSetting::getSettings();
        $this->enabled = $this->settings->enabled;
        $this->hours_after_end = $this->settings->hours_after_end;
        $this->frequency = $this->settings->frequency;
        $this->send_to_passengers = $this->settings->send_to_passengers;
        $this->skip_weekends = $this->settings->skip_weekends;
        $this->max_reminders = $this->settings->max_reminders;
        $this->custom_message = $this->settings->custom_message;
        $this->excluded_asset_types = $this->settings->excluded_asset_types ?? [];
    }

    public function save()
    {
        $this->validate([
            'hours_after_end' => 'required|integer|min:1|max:72',
            'frequency' => 'required|in:hourly,every_4_hours,daily',
            'max_reminders' => 'required|integer|min:1|max:10',
            'custom_message' => 'nullable|string|max:500',
        ]);

        $this->settings->update([
            'enabled' => $this->enabled,
            'hours_after_end' => $this->hours_after_end,
            'frequency' => $this->frequency,
            'send_to_passengers' => $this->send_to_passengers,
            'skip_weekends' => $this->skip_weekends,
            'max_reminders' => $this->max_reminders,
            'custom_message' => $this->custom_message,
            'excluded_asset_types' => $this->excluded_asset_types,
        ]);

        session()->flash('success', 'Reminder settings updated successfully!');
        $this->loadSettings();
    }

    public function runTest()
    {
        try {
            $artisanPath = base_path('artisan');
            $command = "php {$artisanPath} bookings:send-incomplete-reminders 2>&1";
            $output = shell_exec($command);
            $this->testResult = $output ?: 'Command executed successfully with no output.';
            $this->showTestModal = true;
        } catch (\Exception $e) {
            $this->testResult = 'Error: ' . $e->getMessage();
            $this->showTestModal = true;
        }
    }

    public function closeTestModal()
    {
        $this->showTestModal = false;
        $this->testResult = '';
    }

    public function clearLogs()
    {
        if (auth()->user()->hasRole('Super Admin')) {
            BookingReminderLog::truncate();
            session()->flash('success', 'All reminder logs have been cleared.');
        }
    }

    public function render()
    {
        $logs = BookingReminderLog::with(['booking.asset', 'user'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total_sent' => BookingReminderLog::where('status', 'sent')->count(),
            'total_failed' => BookingReminderLog::where('status', 'failed')->count(),
            'last_24h' => BookingReminderLog::where('created_at', '>=', now()->subDay())->count(),
        ];

        return view('livewire.admin.reminder-settings', [
            'logs' => $logs,
            'stats' => $stats,
            'frequencies' => BookingReminderSetting::getFrequencies(),
            'assetTypes' => BookingReminderSetting::getAssetTypes(),
        ]);
    }
}
