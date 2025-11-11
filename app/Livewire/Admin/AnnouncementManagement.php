<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Announcement;
use App\Models\AnnouncementImage;
use Illuminate\Support\Facades\Storage;

class AnnouncementManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $title;
    public $content;
    public $images = [];
    public $existingImages = [];
    public $type = 'info';
    public $priority = 'normal';
    public $is_active = true;
    public $show_banner = true;
    public $start_date;
    public $end_date;

    public $editingId = null;
    public $showModal = false;
    public $showDeleteModal = false;
    public $deleteId = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'images.*' => 'nullable|image|max:2048',
        'type' => 'required|in:info,warning,success,danger',
        'priority' => 'required|in:low,normal,high,urgent',
        'is_active' => 'boolean',
        'show_banner' => 'boolean',
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date|after_or_equal:start_date',
    ];

    public function openCreateModal()
    {
        $this->reset(['title', 'content', 'images', 'existingImages', 'type', 'priority', 'is_active', 'show_banner', 'start_date', 'end_date', 'editingId']);
        $this->type = 'info';
        $this->priority = 'normal';
        $this->is_active = true;
        $this->show_banner = true;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $announcement = Announcement::with('images')->findOrFail($id);
        $this->editingId = $id;
        $this->title = $announcement->title;
        $this->content = $announcement->content;

        // Load existing images (including legacy main image)
        $existingImages = $announcement->images->toArray();
        if ($announcement->image_path) {
            array_unshift($existingImages, [
                'id' => 'legacy',
                'image_path' => $announcement->image_path,
                'order' => -1,
            ]);
        }
        $this->existingImages = $existingImages;

        $this->type = $announcement->type;
        $this->priority = $announcement->priority;
        $this->is_active = $announcement->is_active;
        $this->show_banner = $announcement->show_banner;
        $this->start_date = $announcement->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $announcement->end_date?->format('Y-m-d\TH:i');
        $this->showModal = true;
    }

    public function removeExistingImage($imageId)
    {
        if ($imageId === 'legacy') {
            // Remove legacy main image
            if ($this->editingId) {
                $announcement = Announcement::findOrFail($this->editingId);
                if ($announcement->image_path) {
                    Storage::disk('public')->delete($announcement->image_path);
                    $announcement->update(['image_path' => null]);
                }
            }
        } else {
            // Remove from announcement_images table
            $image = AnnouncementImage::find($imageId);
            if ($image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }

        // Remove from local array
        $this->existingImages = array_filter($this->existingImages, fn($img) => $img['id'] !== $imageId);
        $this->existingImages = array_values($this->existingImages); // Reindex array
    }

    public function removeNewImage($index)
    {
        array_splice($this->images, $index, 1);
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['title', 'content', 'images', 'existingImages', 'type', 'priority', 'is_active', 'show_banner', 'start_date', 'end_date', 'editingId']);
    }

    public function save()
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'content' => $this->content,
            'type' => $this->type,
            'priority' => $this->priority,
            'is_active' => $this->is_active,
            'show_banner' => $this->show_banner,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        if ($this->editingId) {
            $announcement = Announcement::findOrFail($this->editingId);
            $announcement->update($data);
            session()->flash('success', 'Announcement updated successfully!');
        } else {
            $data['created_by'] = auth()->id();
            $announcement = Announcement::create($data);
            session()->flash('success', 'Announcement created successfully!');
        }

        // Handle new images upload
        if (!empty($this->images)) {
            $order = $announcement->images()->count();
            foreach ($this->images as $image) {
                $imagePath = $image->store('announcements', 'public');
                $announcement->images()->create([
                    'image_path' => $imagePath,
                    'order' => $order++,
                ]);
            }
        }

        $this->closeModal();
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        if ($this->deleteId) {
            $announcement = Announcement::with('images')->findOrFail($this->deleteId);

            // Delete main image if exists
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }

            // Delete all additional images
            foreach ($announcement->images as $image) {
                Storage::disk('public')->delete($image->image_path);
            }

            $announcement->delete();
            session()->flash('success', 'Announcement deleted successfully!');
            $this->showDeleteModal = false;
            $this->deleteId = null;
        }
    }

    public function toggleActive($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->update(['is_active' => !$announcement->is_active]);
        session()->flash('success', 'Announcement status updated!');
    }

    public function render()
    {
        $announcements = Announcement::with('creator')
            ->withCount('views')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'total' => Announcement::count(),
            'active' => Announcement::active()->count(),
            'inactive' => Announcement::where('is_active', false)->count(),
        ];

        return view('livewire.admin.announcement-management', [
            'announcements' => $announcements,
            'stats' => $stats,
            'types' => [
                'info' => 'Info',
                'warning' => 'Warning',
                'success' => 'Success',
                'danger' => 'Danger',
            ],
            'priorities' => [
                'low' => 'Low',
                'normal' => 'Normal',
                'high' => 'High',
                'urgent' => 'Urgent',
            ],
        ]);
    }
}
