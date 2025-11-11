<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Announcement;

class AnnouncementBanner extends Component
{
    public $currentIndex = 0;
    public $announcements;

    public function mount()
    {
        $this->loadAnnouncements();
    }

    public function loadAnnouncements()
    {
        $this->announcements = Announcement::with('images')
            ->active()
            ->banner()
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($announcement) {
                // Convert to array and add secure URLs
                $data = $announcement->toArray();

                // Add secure URL for main image
                if ($announcement->image_path) {
                    $data['secure_image_url'] = $announcement->secure_image_url;
                }

                // Add secure URLs for additional images
                if ($announcement->images) {
                    $data['images'] = $announcement->images->map(function ($image) {
                        $imageData = $image->toArray();
                        $imageData['secure_url'] = $image->secure_url;
                        return $imageData;
                    })->toArray();
                }

                return $data;
            });

        // Mark as viewed by current user (need to reload from DB)
        $announcementIds = $this->announcements->pluck('id');
        Announcement::whereIn('id', $announcementIds)->get()->each(function ($announcement) {
            $announcement->markAsViewedBy(auth()->user());
        });
    }

    public function nextSlide()
    {
        if ($this->announcements->count() > 0) {
            $this->currentIndex = ($this->currentIndex + 1) % $this->announcements->count();
        }
    }

    public function prevSlide()
    {
        if ($this->announcements->count() > 0) {
            $this->currentIndex = ($this->currentIndex - 1 + $this->announcements->count()) % $this->announcements->count();
        }
    }

    public function goToSlide($index)
    {
        $this->currentIndex = $index;
    }

    public function dismiss($announcementId)
    {
        $announcement = Announcement::find($announcementId);
        if ($announcement) {
            $announcement->markAsReadBy(auth()->user());
        }

        // Remove from current collection
        $this->announcements = $this->announcements->filter(function($item) use ($announcementId) {
            return $item->id !== $announcementId;
        });

        // Reset index if needed
        if ($this->currentIndex >= $this->announcements->count() && $this->announcements->count() > 0) {
            $this->currentIndex = 0;
        }

        // If no announcements left, reload to check for new ones
        if ($this->announcements->count() === 0) {
            $this->loadAnnouncements();
        }
    }

    public function render()
    {
        return view('livewire.announcement-banner');
    }
}
