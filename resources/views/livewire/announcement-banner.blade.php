<div class="relative overflow-hidden"
     x-data="{
         currentIndex: 0,
         imageIndex: 0,
         announcements: @js($announcements && $announcements->count() > 0 ? $announcements->values()->toArray() : []),
         selectedAnnouncement: null,
         viewDetails(announcement) {
             this.selectedAnnouncement = announcement;
             this.imageIndex = 0;
             $flux.modal('announcement-details').show();
         },
         goNext() {
             this.currentIndex = (this.currentIndex + 1) % this.announcements.length;
         },
         goPrev() {
             this.currentIndex = (this.currentIndex - 1 + this.announcements.length) % this.announcements.length;
         },
         dismiss(id) {
             // Remove from local array immediately for instant UI feedback
             this.announcements = this.announcements.filter(a => a.id !== id);
             if (this.currentIndex >= this.announcements.length && this.announcements.length > 0) {
                 this.currentIndex = 0;
             }

             // Mark as read in the background via API
             fetch('/api/announcements/' + id + '/dismiss', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                     'Accept': 'application/json'
                 }
             }).catch(() => console.log('Announcement dismissed locally'));
         },
         getAllImages() {
             if (!this.selectedAnnouncement) {
                 return [];
             }

             let images = [];

             // Add legacy main image first (use secure URL)
             if (this.selectedAnnouncement.secure_image_url) {
                 images.push({
                     url: this.selectedAnnouncement.secure_image_url,
                     isSecure: true
                 });
             }

             // Add additional images (use secure URLs)
             if (this.selectedAnnouncement.images) {
                 if (Array.isArray(this.selectedAnnouncement.images) && this.selectedAnnouncement.images.length > 0) {
                     this.selectedAnnouncement.images.forEach(img => {
                         if (img && img.secure_url) {
                             images.push({
                                 url: img.secure_url,
                                 isSecure: true
                             });
                         }
                     });
                 }
             }

             return images;
         },
         getFirstImage(announcement) {
             // Get first image from all available images for banner display
             let images = [];

             // Add legacy main image first (use secure URL)
             if (announcement.secure_image_url) {
                 images.push(announcement.secure_image_url);
             }

             // Add additional images (use secure URLs)
             if (announcement.images && Array.isArray(announcement.images) && announcement.images.length > 0) {
                 announcement.images.forEach(img => {
                     if (img && img.secure_url) {
                         images.push(img.secure_url);
                     }
                 });
             }

             return images.length > 0 ? images[0] : null;
         }
     }"
     wire:ignore>
    @if($announcements && $announcements->count() > 0)
    <!-- Carousel Container -->
    <div class="relative h-[200px]" x-show="announcements.length > 0">
        <template x-for="(announcement, index) in announcements" :key="announcement.id">
            <div
                x-show="currentIndex === index"
                x-transition:enter="transition ease-out duration-500 absolute inset-0"
                x-transition:enter-start="opacity-0 transform translate-x-full"
                x-transition:enter-end="opacity-100 transform translate-x-0"
                x-transition:leave="transition ease-in duration-500 absolute inset-0"
                x-transition:leave-start="opacity-100 transform translate-x-0"
                x-transition:leave-end="opacity-0 transform -translate-x-full"
                class="w-full"
                style="display: none;"
            >
                <!-- Announcement Card -->
                <flux:card class="dark:bg-zinc-900 border-l-4"
                    ::class="{
                        'border-blue-500': announcement.type === 'info',
                        'border-amber-500': announcement.type === 'warning',
                        'border-green-500': announcement.type === 'success',
                        'border-red-500': announcement.type === 'danger'
                    }"
                >
                    <div class="p-4 sm:p-6">
                        <div class="flex items-start gap-4">
                            <!-- Image (if available) - Fixed size -->
                            <template x-if="getFirstImage(announcement)">
                                <div class="flex-shrink-0">
                                    <img :src="getFirstImage(announcement)"
                                         :alt="announcement.title"
                                         class="w-26 h-26 object-cover rounded-lg border border-gray-200 dark:border-zinc-700">
                                </div>
                            </template>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-2 flex-wrap">

                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate" x-text="announcement.title"></h3>

                                    <template x-if="announcement.priority === 'urgent'">
                                        <flux:badge size="sm" color="red">Urgent</flux:badge>
                                    </template>
                                    <template x-if="announcement.priority === 'high'">
                                        <flux:badge size="sm" color="amber">High Priority</flux:badge>
                                    </template>
                                </div>

                                <!-- Truncated content (max 2 lines) -->
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2" x-text="announcement.content"></p>

                                <div class="flex items-center justify-start mt-2">
                                    {{-- <template x-if="announcement.end_date">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <span>Valid until: </span><span x-text="new Date(announcement.end_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                                        </div>
                                    </template> --}}

                                    <!-- View Details Button -->
                                    {{-- <button
                                        @click="viewDetails(announcement)"
                                        type="button"
                                        class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 underline transition-colors"
                                    >
                                        View Details
                                    </button> --}}
                                    <flux:button variant="filled" size="xs" @click="viewDetails(announcement)">View Details</flux:button>
                                </div>
                            </div>

                            <!-- Dismiss Button -->
                            <button
                                @click="dismiss(announcement.id)"
                                type="button"
                                class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                                title="Dismiss"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </flux:card>
            </div>
        </template>
    </div>

    <!-- Navigation Controls (only show if more than 1 announcement) -->
    <div x-show="announcements.length > 1">
        <div class="flex items-center justify-between mt-4">
            <!-- Previous Button -->
            <button
                @click="goPrev()"
                type="button"
                class="p-2 rounded-lg bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 transition-colors"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <!-- Dots Indicator -->
            <div class="flex gap-2">
                <template x-for="(announcement, idx) in announcements" :key="'dot-' + announcement.id">
                    <button
                        @click="currentIndex = idx"
                        type="button"
                        :class="currentIndex === idx ? 'w-8 bg-blue-500' : 'w-2 bg-gray-300 dark:bg-zinc-600 hover:bg-gray-400 dark:hover:bg-zinc-500'"
                        class="h-2 rounded-full transition-all duration-300"
                    ></button>
                </template>
            </div>

            <!-- Next Button -->
            <button
                @click="goNext()"
                type="button"
                class="p-2 rounded-lg bg-white dark:bg-zinc-800 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-700 transition-colors"
            >
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Auto-rotate script -->
        <div x-init="
            setInterval(() => {
                if (announcements.length > 0) {
                    goNext();
                }
            }, 8000);
        "></div>
    </div>

    <!-- Announcement Details Modal -->
    <flux:modal name="announcement-details" class="min-w-[600px]">
        <div x-show="selectedAnnouncement">
            <!-- Header with Icon and Title -->
            <div class="flex items-start gap-3 mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white" x-text="selectedAnnouncement ? selectedAnnouncement.title : ''"></h2>
                        <template x-if="selectedAnnouncement && selectedAnnouncement.priority === 'urgent'">
                            <flux:badge size="sm" color="red">Urgent</flux:badge>
                        </template>
                        <template x-if="selectedAnnouncement && selectedAnnouncement.priority === 'high'">
                            <flux:badge size="sm" color="amber">High Priority</flux:badge>
                        </template>
                    </div>
                </div>
            </div>

            <flux:separator class="my-4" />

            <!-- Image Carousel (if images available) -->
            <div x-show="getAllImages().length > 0" class="mb-4">
                <div class="relative">
                    <!-- Images -->
                    <div class="relative h-96 overflow-hidden rounded-lg border border-gray-200 dark:border-zinc-700 group">
                        <template x-for="(imageObj, idx) in getAllImages()" :key="'img-' + idx">
                            <div
                                x-show="imageIndex === idx"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100"
                                class="absolute inset-0 flex items-center justify-center bg-gray-50 dark:bg-zinc-800 cursor-pointer"
                                style="display: none;"
                                @click="window.open(imageObj.url, '_blank')"
                                title="Click to view full size in new tab"
                            >
                                <img :src="imageObj.url"
                                     :alt="'Image ' + (idx + 1)"
                                     class="max-w-full max-h-full object-contain">
                                <!-- Zoom Hint Overlay -->
                                <div class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200 flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-white dark:bg-zinc-800 rounded-lg px-3 py-2 shadow-lg">
                                        <div class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                            </svg>
                                            <span class="font-medium">Click to view full size</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Navigation Controls (only show if more than 1 image) -->
                    <template x-if="getAllImages().length > 1">
                        <div>
                            <!-- Previous Button -->
                            <button
                                @click="let len = getAllImages().length; imageIndex = imageIndex === 0 ? len - 1 : imageIndex - 1"
                                type="button"
                                class="absolute left-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 hover:bg-black/70 text-white transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>

                            <!-- Next Button -->
                            <button
                                @click="let len = getAllImages().length; imageIndex = imageIndex === len - 1 ? 0 : imageIndex + 1"
                                type="button"
                                class="absolute right-2 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 hover:bg-black/70 text-white transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>

                            <!-- Dots Indicator -->
                            <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-2">
                                <template x-for="(imageObj, idx) in getAllImages()" :key="'dot-' + idx">
                                    <button
                                        @click="imageIndex = idx"
                                        type="button"
                                        :class="imageIndex === idx ? 'bg-white' : 'bg-white/50 hover:bg-white/75'"
                                        class="w-2 h-2 rounded-full transition-all duration-300"
                                    ></button>
                                </template>
                            </div>

                            <!-- Image Counter -->
                            <div class="absolute top-3 right-3 px-2 py-1 bg-black/60 text-white text-xs rounded-lg">
                                <span x-text="imageIndex + 1"></span> / <span x-text="getAllImages().length"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Full content -->
            <div class="mb-4">
                <p class="text-base text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed" x-text="selectedAnnouncement ? selectedAnnouncement.content : ''"></p>
            </div>

            <!-- End date info -->
            {{-- <template x-if="selectedAnnouncement && selectedAnnouncement.end_date">
                <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="text-sm text-blue-900 dark:text-blue-200">
                            <span class="font-semibold">Valid until:</span>
                            <span x-text="selectedAnnouncement.end_date ? new Date(selectedAnnouncement.end_date).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' }) : ''"></span>
                        </span>
                    </div>
                </div>
            </template> --}}

            <flux:separator class="my-4" />

            <!-- Action buttons -->
            <div class="flex justify-end gap-2">
                <flux:button variant="ghost" @click="$flux.modal('announcement-details').close()">
                    Close
                </flux:button>
                <flux:button
                    @click="selectedAnnouncement && dismiss(selectedAnnouncement.id); $flux.modal('announcement-details').close()"
                    variant="primary"
                >
                    Dismiss Announcement
                </flux:button>
            </div>
        </div>
    </flux:modal>
    @endif
</div>
