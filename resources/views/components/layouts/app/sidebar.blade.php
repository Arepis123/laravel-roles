<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
            <flux:sidebar.header>
 
                <flux:sidebar.brand
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    logo="{{ asset('image/logo-clab.png') }}"
                    logo:dark="{{ asset('image/logo-clab.png') }}"
                    name="e-Booking CLAB"
                />                
                <flux:sidebar.collapse class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
            </flux:sidebar.header>

            <flux:sidebar.nav>
                <!-- Main Menu Section -->
                <div class="px-3 py-2 in-data-flux-sidebar-collapsed-desktop:hidden">
                    <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('MAIN') }}</h3>
                </div>
                <flux:sidebar.item icon="house" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:sidebar.item>
                <flux:sidebar.item icon="calendar" :href="route('bookings.index.user')" :current="request()->routeIs('bookings.index.user')" wire:navigate>{{ __('My Bookings') }}</flux:sidebar.item>

                @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                    <!-- Admin Section -->
                    <div class="px-3 py-2 mt-4 in-data-flux-sidebar-collapsed-desktop:hidden">
                        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('ADMIN') }}</h3>
                    </div>
                    @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                    <flux:sidebar.item icon="calendar-days" :href="route('bookings.index')" :current="request()->routeIs('bookings.index')" wire:navigate tooltip="All Bookings">
                        <div class="flex items-center justify-between w-full">
                            <span>{{ __('All Bookings') }}</span>
                            <livewire:pending-bookings-badge wire:poll.30s="loadCount" />
                        </div>
                    </flux:sidebar.item>
                    @endif
                    @if(auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('user.edit') || auth()->user()->can('user.delete'))
                    <flux:sidebar.item icon="user" :href="route('users.index')" :current="request()->routeIs('users.index')" wire:navigate>{{ __('User') }}</flux:sidebar.item>
                    @endif
                    @if(auth()->user()->hasRole(['Super Admin']))
                    <flux:sidebar.item icon="key" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>{{ __('Role') }}</flux:sidebar.item>
                    @endif
                    @if(auth()->user()->can('asset.view') || auth()->user()->can('asset.create') || auth()->user()->can('asset.edit') || auth()->user()->can('asset.delete'))
                    {{-- <flux:sidebar.item icon="squares-2x2" :href="route('assets')" :current="request()->routeIs('assets')" wire:navigate>{{ __('Asset') }}</flux:sidebar.item> --}}
                    <flux:sidebar.group expandable icon="car-front" heading="Assets" class="grid">
                        <flux:sidebar.item icon="circle-small" :href="route('assets')">Lists</flux:sidebar.item>
                        <flux:sidebar.item icon="circle-small" :href="route('assets.qr-codes')">QR Code</flux:sidebar.item>
                    </flux:sidebar.group>                    
                    @endif
                    @if(auth()->user()->can('report.view') || auth()->user()->can('report.create') || auth()->user()->can('report.edit') || auth()->user()->can('report.delete'))
                    <flux:sidebar.item icon="file-text" :href="route('reports')" :current="request()->routeIs('reports')" wire:navigate>{{ __('Report') }}</flux:sidebar.item>
                    @endif
                @endif

                @if(auth()->user()->hasPermissionTo('vehicle.view'))
                    <!-- Vehicle Management Section -->
                    <div class="px-3 py-2 mt-4 in-data-flux-sidebar-collapsed-desktop:hidden">
                        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-400 uppercase tracking-wider">{{ __('VEHICLE MANAGEMENT') }}</h3>
                    </div>
                    @if(auth()->user()->can('vehicle.view') || auth()->user()->can('vehicle.create') || auth()->user()->can('vehicle.edit') || auth()->user()->can('vehicle.delete'))
                    <flux:sidebar.item icon="fuel" :href="route('vehicles.fuel')" :current="request()->routeIs('vehicles.fuel')" wire:navigate>{{ __('Fuel Logs') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="gauge" :href="route('vehicles.odometer')" :current="request()->routeIs('vehicles.odometer')" wire:navigate>{{ __('Odometer Logs') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="wrench" :href="route('vehicles.maintenance')" :current="request()->routeIs('vehicles.maintenance')" wire:navigate>{{ __('Maintenance') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-check" :href="route('vehicles.checkup')" :current="request()->routeIs('vehicles.checkup')" wire:navigate>{{ __('Vehicle Checkup') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="clipboard-list" :href="route('vehicles.checkup-templates')" :current="request()->routeIs('vehicles.checkup-templates')" wire:navigate>{{ __('Checkup Templates') }}</flux:sidebar.item>
                    <flux:sidebar.item icon="presentation-chart-line" :href="route('vehicles.analytics')" :current="request()->routeIs('vehicles.analytics')" wire:navigate>{{ __('Analytics') }}</flux:sidebar.item>
                    @endif
                @endif

            </flux:sidebar.nav>

            <flux:sidebar.spacer />

            <flux:sidebar.nav>
                <flux:sidebar.item icon="book-open-text" href="/user-manual" target="_blank">User Manual</flux:sidebar.item>
            </flux:sidebar.nav>

            <flux:dropdown position="top" align="start" class="max-lg:hidden">
                <flux:sidebar.profile
                    :name="auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A'"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar name="{{ auth()->user() ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}" />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile Header -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()
                    ? collect(explode(' ', preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name)))
                        ->map(fn($part) => strtoupper(substr($part, 0, 1)))
                        ->implode('')
                    : 'NA'"
                icon-trailing="chevron-down"
            />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <flux:avatar class="flex h-full w-full items-center justify-center" name="{{ auth()->user() ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}" />
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A' }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>