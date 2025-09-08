<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>

            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('MENU')" class="grid">
                    <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>
                    <flux:navlist.item icon="calendar" :href="route('bookings.index.user')" :current="request()->routeIs('bookings.index.user')" wire:navigate>{{ __('Booking') }}</flux:navlist.item> 
                </flux:navlist.group>
            </flux:navlist>

            @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('ADMIN')" class="grid">
                    @if(auth()->user()->hasRole(['Admin', 'Super Admin']))
                    <flux:navlist.item icon="calendar" :href="route('bookings.index')" :current="request()->routeIs('bookings.index')" wire:navigate>
                        <span class="flex items-center justify-between w-full">
                            <span>{{ __('Booking') }}</span>
                            {{-- Livewire component with real-time updates --}}
                            <livewire:pending-bookings-badge wire:poll.30s="loadCount" />
                        </span>
                    </flux:navlist.item>
                    @endif                    
                    @if(auth()->user()->can('user.view') || auth()->user()->can('user.create') || auth()->user()->can('user.edit') || auth()->user()->can('user.delete'))
                    <flux:navlist.item icon="user" :href="route('users.index')" :current="request()->routeIs('users.index')" wire:navigate>{{ __('User') }}</flux:navlist.item>
                    @endif                    
                    @if(auth()->user()->hasRole(['Super Admin']))
                    <flux:navlist.item icon="key" :href="route('roles.index')" :current="request()->routeIs('roles.index')" wire:navigate>{{ __('Role') }}</flux:navlist.item>
                    @endif
                    @if(auth()->user()->can('asset.view') || auth()->user()->can('asset.create') || auth()->user()->can('asset.edit') || auth()->user()->can('asset.delete'))                    
                    <flux:navlist.item icon="squares-2x2" :href="route('assets')" :current="request()->routeIs('assets')" wire:navigate>{{ __('Asset') }}</flux:navlist.item>                    
                    @endif
                    @if(auth()->user()->can('report.view') || auth()->user()->can('report.create') || auth()->user()->can('report.edit') || auth()->user()->can('report.delete'))                                        
                    <flux:navlist.item icon="document" :href="route('reports')" :current="request()->routeIs('reports')" wire:navigate>{{ __('Report') }}</flux:navlist.item>                    
                    @endif
                </flux:navlist.group>
            </flux:navlist>
            @endif 
            
            @if(auth()->user()->hasPermissionTo('vehicle.view'))
            <flux:navlist variant="outline">
                <flux:navlist.group :heading="__('VEHICLE MANAGEMENT')" class="grid">
                    @if(auth()->user()->can('asset.view') || auth()->user()->can('asset.create') || auth()->user()->can('asset.edit') || auth()->user()->can('asset.delete'))
                    <flux:navlist.item icon="fire" :href="route('vehicles.fuel')" :current="request()->routeIs('vehicles.fuel')" wire:navigate>{{ __('Fuel Logs') }}</flux:navlist.item>
                    <flux:navlist.item icon="chart-bar" :href="route('vehicles.odometer')" :current="request()->routeIs('vehicles.odometer')" wire:navigate>{{ __('Odometer Logs') }}</flux:navlist.item>
                    <flux:navlist.item icon="wrench-screwdriver" :href="route('vehicles.maintenance')" :current="request()->routeIs('vehicles.maintenance')" wire:navigate>{{ __('Maintenance') }}</flux:navlist.item>
                    <flux:navlist.item icon="presentation-chart-line" :href="route('vehicles.analytics')" :current="request()->routeIs('vehicles.analytics')" wire:navigate>{{ __('Analytics') }}</flux:navlist.item>
                    @endif
                </flux:navlist.group>
            </flux:navlist>
            @endif       

            <flux:spacer />


            <flux:navlist variant="outline">
                <flux:navlist.item icon="book-open-text" href="/user-manual" target="_blank">User Manual</flux:navlist.item>
            </flux:navlist>            

            <!-- Desktop User Menu -->
            <flux:dropdown position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name ? preg_replace('/\s+(BIN|BINTI)\b.*/i', '', auth()->user()->name) : 'N/A'"                    
                    icon-trailing="chevrons-up-down"
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
        </flux:sidebar>

        <!-- Mobile User Menu -->
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