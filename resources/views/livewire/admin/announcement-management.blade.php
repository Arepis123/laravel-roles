<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">        
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Announcement Management</h1>
                <p class="text-gray-600 mt-1 dark:text-gray-400">Create and manage system-wide announcements for users</p>
            </div>
            <div class="flex justify-between items-center">
                <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                    Create Announcement
                </flux:button>
            </div>               
        </div>
        <flux:separator variant="subtle" class="my-4" />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
        <!-- Total Announcements -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Total</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Active Announcements -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-green-100 dark:bg-green-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Active</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['active'] }}</flux:text>
                </div>
            </div>
        </flux:card>

        <!-- Inactive Announcements -->
        <flux:card class="p-4 sm:p-6 dark:bg-zinc-900">
            <div class="flex flex-col items-center justify-center text-center sm:flex-row sm:items-center sm:justify-start sm:text-left">
                <div class="p-2 bg-gray-100 dark:bg-gray-900/50 rounded-lg hidden sm:block">
                    <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                    </svg>
                </div>
                <div class="ml-0 sm:ml-4">
                    <flux:heading class="text-gray-500 dark:text-gray-400 font-medium">Inactive</flux:heading>
                    <flux:text class="text-xl font-semibold text-gray-900 dark:text-white">{{ $stats['inactive'] }}</flux:text>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div x-data="{ visible: true }" x-show="visible" x-collapse class="mb-4">
            <div x-show="visible" x-transition>
                <flux:callout icon="check-circle" variant="success" heading="{{ session('success') }}">
                    <x-slot name="controls">
                        <flux:button icon="x-mark" variant="ghost" x-on:click="visible = false" />
                    </x-slot>
                </flux:callout>
            </div>
        </div>
    @endif

    <!-- Announcements List -->
    <flux:card class="dark:bg-zinc-900">
        <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
            <flux:heading>Announcements</flux:heading>
            <flux:text class="text-gray-600 dark:text-gray-400">Manage all system announcements</flux:text>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Announcement</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Views</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($announcements as $announcement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-800">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-900 dark:text-neutral-200">{{ $loop->iteration + ($announcements->currentPage() - 1) * $announcements->perPage() }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    @if($announcement->image_path)
                                        <img src="{{ asset('storage/' . $announcement->image_path) }}"
                                             alt="{{ $announcement->title }}"
                                             class="w-12 h-12 object-cover rounded border border-gray-200 dark:border-zinc-700 flex-shrink-0">
                                    @endif
                                    <div class="min-w-0 flex-1">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white block">{{ $announcement->title }}</span>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mt-1">{{ $announcement->content }}</p>
                                        @if($announcement->start_date || $announcement->end_date)
                                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-400 dark:text-gray-500">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                @if($announcement->start_date)
                                                    {{ $announcement->start_date->format('M d') }}
                                                @endif
                                                @if($announcement->start_date && $announcement->end_date)
                                                    -
                                                @endif
                                                @if($announcement->end_date)
                                                    {{ $announcement->end_date->format('M d, Y') }}
                                                @endif
                                            </div>
                                        @endif
                                        @if($announcement->show_banner)
                                            <div class="flex gap-1 mt-2">
                                                <span class="text-xs bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-300 px-2 py-0.5 rounded">Banner</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm" :color="$announcement->type_badge_color">{{ ucfirst($announcement->type) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <flux:badge size="sm" :color="$announcement->priority_badge_color">{{ ucfirst($announcement->priority) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="toggleActive({{ $announcement->id }})" type="button">
                                    @if($announcement->is_active)
                                        <flux:badge size="sm" color="lime" icon="check-circle">Active</flux:badge>
                                    @else
                                        <flux:badge size="sm" color="zinc" icon="x-circle">Inactive</flux:badge>
                                    @endif
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">
                                {{ $announcement->views_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex gap-2">                                    
                                    <flux:button wire:click="openEditModal({{ $announcement->id }})" size="sm" variant="ghost" title="Edit">
                                        <flux:icon name="pencil" class="w-4 h-4" />
                                    </flux:button>                                                                              
                                    <flux:button wire:click="confirmDelete({{ $announcement->id }})" variant="ghost" size="sm" title="Delete" class="text-red-600 hover:text-red-800">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </flux:button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                No announcements available for the selected period.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($announcements->hasPages())
            <div class="p-4 border-t border-gray-200 dark:border-zinc-700">
                {{ $announcements->links() }}
            </div>
        @endif
    </flux:card>

    <!-- Create/Edit Modal -->
    <flux:modal wire:model="showModal" class="min-w-[42rem]">
        <form wire:submit.prevent="save">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ $editingId ? 'Edit Announcement' : 'Create New Announcement' }}</flux:heading>
                    <flux:subheading>{{ $editingId ? 'Update announcement details' : 'Add a new system announcement' }}</flux:subheading>
                </div>

                <!-- Title -->
                <flux:field>
                    <flux:label>Title</flux:label>
                    <flux:input wire:model="title" placeholder="Enter announcement title" />
                    <flux:error name="title" />
                </flux:field>

                <!-- Content -->
                <flux:field>
                    <flux:label>Content</flux:label>
                    <flux:textarea wire:model="content" rows="4" placeholder="Enter announcement content..." />
                    <flux:description>Main message that will be displayed to users</flux:description>
                    <flux:error name="content" />
                </flux:field>

                <!-- Images Upload -->
                <flux:file-upload wire:model="images" label="Images (Optional)" multiple>
                    <flux:file-upload.dropzone
                        heading="Drop images here or click to browse"
                        text="PNG, JPG or GIF up to 2MB each"
                        with-progress
                    />
                </flux:file-upload>

                <flux:description>Add one or multiple images for a carousel slideshow</flux:description>
                <flux:error name="images.*" />

                <!-- Existing Images -->
                @if(!empty($existingImages))
                    <div class="mt-3 flex flex-col gap-2">
                        @foreach($existingImages as $img)
                            <flux:file-item
                                heading="Existing Image {{ $loop->iteration }}"
                                :image="asset('storage/' . $img['image_path'])"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove
                                        wire:click="removeExistingImage('{{ $img['id'] }}')"
                                    />
                                </x-slot>
                            </flux:file-item>
                        @endforeach
                    </div>
                @endif

                <!-- New Images Preview -->
                @if(!empty($images))
                    <div class="mt-3 flex flex-col gap-2">
                        @foreach($images as $index => $image)
                            <flux:file-item
                                :heading="$image->getClientOriginalName()"
                                :image="$image->temporaryUrl()"
                                :size="$image->getSize()"
                            >
                                <x-slot name="actions">
                                    <flux:file-item.remove
                                        wire:click="removeNewImage({{ $index }})"
                                    />
                                </x-slot>
                            </flux:file-item>
                        @endforeach
                    </div>
                @endif

                <!-- Type and Priority -->
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Type</flux:label>
                        <flux:select variant="listbox" wire:model="type">
                            @foreach($types as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="type" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Priority</flux:label>
                        <flux:select variant="listbox" wire:model="priority">
                            @foreach($priorities as $value => $label)
                                <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:error name="priority" />
                    </flux:field>
                </div>

                <!-- Display Options -->
                <div class="grid grid-cols-2 gap-4">
                    <flux:field variant="inline">
                        <flux:checkbox wire:model="is_active" />
                        <flux:label>Active</flux:label>
                    </flux:field>

                    <flux:field variant="inline">
                        <flux:checkbox wire:model="show_banner" />
                        <flux:label>Show Banner</flux:label>
                    </flux:field>
                </div>

                <!-- Schedule -->
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Start Date (Optional)</flux:label>
                        <flux:input type="datetime-local" wire:model="start_date" />
                        <flux:description>When to start showing</flux:description>
                        <flux:error name="start_date" />
                    </flux:field>

                    <flux:field>
                        <flux:label>End Date (Optional)</flux:label>
                        <flux:input type="datetime-local" wire:model="end_date" />
                        <flux:description>When to stop showing</flux:description>
                        <flux:error name="end_date" />
                    </flux:field>
                </div>

                <div class="flex gap-2 justify-end">
                    <flux:button wire:click="closeModal" variant="ghost" type="button">Cancel</flux:button>
                    <flux:button type="submit" variant="primary">{{ $editingId ? 'Update' : 'Create' }}</flux:button>
                </div>
            </div>
        </form>
    </flux:modal>

    <!-- Delete Confirmation Modal -->
    <flux:modal wire:model="showDeleteModal" class="min-w-[28rem]">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Delete Announcement</flux:heading>
                <flux:subheading>Are you sure you want to delete this announcement? This action cannot be undone.</flux:subheading>
            </div>

            <div class="flex gap-2 justify-end">
                <flux:button wire:click="$set('showDeleteModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="delete" variant="danger">Delete</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
