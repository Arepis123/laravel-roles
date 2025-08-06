<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Booking') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage my booking here') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>
   
    @session('success')
        <div>
            <flux:callout variant="success" icon="check-circle" heading="{{ $value }}" />
        </div>
    @endsession    

    @can('book.create')
    <div class="py-3">
        <flux:button variant="primary" href="{{ route('bookings.create') }}">New Booking</flux:button>
    </div>
    @endcan

    <div class="border border-gray-200 rounded-xl shadow-2xs p-4 dark:bg-neutral-800 dark:border-neutral-700">
        <div class="flex flex-col">
        <div class="-m-1.5 overflow-x-auto">
            <div class="p-1.5 min-w-full inline-block align-middle">
            <div class="overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-neutral-700">
                <thead>
                    <tr>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">No</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Type</th>   
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Name</th> 
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Start Time</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">End Time</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Booked By</th>
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Created</th>  
                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Status</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase dark:text-neutral-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-neutral-700">
                    @foreach ($bookings as $booking)
                        <tr class="hover:bg-gray-100 dark:hover:bg-neutral-700">
                            <td class="px-6 py-2 whitespace-nowrap text-sm font-medium text-gray-800 dark:text-neutral-200">{{ $loop->iteration }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">                                
                                @if ($booking->asset_type_label == 'Vehicle')                            
                                <span class="inline-flex items-center px-2 py-1 gap-2 rounded bg-gray-200 text-s font-medium text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="m20.772 10.156-1.368-4.105A2.995 2.995 0 0 0 16.559 4H7.441a2.995 2.995 0 0 0-2.845 2.051l-1.368 4.105A2.003 2.003 0 0 0 2 12v5c0 .753.423 1.402 1.039 1.743-.013.066-.039.126-.039.195V21a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-2h12v2a1 1 0 0 0 1 1h1a1 1 0 0 0 1-1v-2.062c0-.069-.026-.13-.039-.195A1.993 1.993 0 0 0 22 17v-5c0-.829-.508-1.541-1.228-1.844zM4 17v-5h16l.002 5H4zM7.441 6h9.117c.431 0 .813.274.949.684L18.613 10H5.387l1.105-3.316A1 1 0 0 1 7.441 6z"></path><circle cx="6.5" cy="14.5" r="1.5"></circle><circle cx="17.5" cy="14.5" r="1.5"></circle></svg>
                                    <span class="items-center">{{ $booking->asset_type_label }}</span>
                                </span>
                                @endif
                                @if ($booking->asset_type_label == 'IT Asset')                            
                                <span class="inline-flex items-center px-2 py-1 gap-2 rounded bg-gray-200 text-s font-medium text-gray-700">                                    
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M20 17.722c.595-.347 1-.985 1-1.722V5c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2v11c0 .736.405 1.375 1 1.722V18H2v2h20v-2h-2v-.278zM5 16V5h14l.002 11H5z"></path></svg>
                                    <span class="items-center">{{ $booking->asset_type_label }}</span>
                                </span>
                                @endif      
                                @if ($booking->asset_type_label == 'Meeting Room')                            
                                <span class="inline-flex items-center px-2 py-1 gap-2 rounded bg-gray-200 text-s font-medium text-gray-700">                                    
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" style="fill: rgba(0, 0, 0, 1);transform: ;msFilter:;"><path d="M19 2H9c-1.103 0-2 .897-2 2v6H5c-1.103 0-2 .897-2 2v9a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V4c0-1.103-.897-2-2-2zM5 12h6v8H5v-8zm14 8h-6v-8c0-1.103-.897-2-2-2H9V4h10v16z"></path><path d="M11 6h2v2h-2zm4 0h2v2h-2zm0 4.031h2V12h-2zM15 14h2v2h-2zm-8 .001h2v2H7z"></path></svg>
                                    <span class="items-center">{{ $booking->asset_type_label }}</span>
                                </span>
                                @endif                              
                                {{-- <flux:badge class="" icon="truck">{{ $booking->asset_type_label }}</flux:badge>  --}}
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                @if ($booking->asset_type_label == 'Vehicle')
                                    {{ $booking->asset?->model ?? '-' }}
                                @else
                                    {{ $booking->asset?->name ?? '-' }}
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($booking->start_time)->format('M d, h:i A') }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($booking->end_time)->format('M d, h:i A') }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                <div class="flex items-center gap-2">
                                    <flux:avatar size="xs" color="auto" name="{{ $booking->user ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $booking->user->name) : 'N/A' }}" />
                                    <span class="max-md:hidden">{{ $booking->user ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', $booking->user->name) : 'No user found' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">{{ \Carbon\Carbon::parse($booking->created_at)->format('M d, h:i A') }}</td>
                            <td class="px-6 py-2 whitespace-nowrap text-sm text-gray-800 dark:text-neutral-200">
                                @if ($booking->status == 'pending')
                                    <flux:badge color="yellow">{{ ucwords($booking->status) }}</flux:badge> 
                                @elseif ($booking->status == 'cancel')
                                    <flux:badge color="red">{{ ucwords($booking->status) }}</flux:badge> 
                                @elseif ($booking->status == 'done')
                                    <flux:badge color="green">{{ ucwords($booking->status) }}</flux:badge>
                                @endif
                            </td>
                            <td class="px-6 py-2 whitespace-nowrap text-center text-sm font-medium">                              
                                <flux:button.group>
                                        <flux:button size="sm">View</flux:button>
                                    <flux:dropdown>
                                        <flux:button icon="chevron-down" size="sm" ></flux:button>                                           
                                        <flux:menu>
                                            <flux:menu.submenu heading="Change status">
                                                <flux:menu.radio.group position="bottom" align="center">
                                                    <flux:menu.radio :checked="$booking->status == 'pending'">Pending</flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'cancel'">Cancel</flux:menu.radio>
                                                    <flux:menu.radio :checked="$booking->status == 'done'">Done</flux:menu.radio>
                                                </flux:menu.radio.group>
                                            </flux:menu.submenu>
                                        </flux:menu>
                                    </flux:dropdown>                                                                                                           
                                </flux:button.group>
                                
                                {{-- @can('book.view')
                                <flux:button size="sm" href="{{ route('roles.show', $role->id) }}">View</flux:button>
                                @endcan
                                @can('book.edit')
                                <flux:button size="sm" href="{{ route('roles.edit', $role->id) }}">Edit</flux:button>   
                                @endcan 
                                @can('book.delete')                                
                                <flux:modal.trigger name="delete-role">
                                    <flux:button variant="danger" size="sm" wire:click="confirmDelete({{ $role->id }})">Delete</flux:button>
                                </flux:modal.trigger>  
                                @endcan                       --}}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                </table>
            </div>
            </div>
        </div>
        </div>
    </div>

</div>

