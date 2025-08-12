<div class="min-h-screen flex">
    <div class="hidden lg:block flex-1 bg-cover bg-center"
         style="background-image: url('{{ asset('image/background-ebookingclab-3.jpg') }}');">
    </div>

    <div class="w-full lg:w-2/5 flex items-center justify-center">
        <div class="w-full max-w-md p-10">

            <div class="text-center mb-5">
                <img src="{{ asset('image/logo-clab.png') }}" alt="CLAB Logo" class="mx-auto w-12 mb-3">
                <h1 class="text-3xl font-bold">e-BOOKING</h1>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Optimizing Workplace Reservations</p>
            </div>

            <x-auth-header :title="''" :description="__('Enter your details below to create your account')" />

            <x-auth-session-status class="text-center" :status="session('status')" />

            <form wire:submit="register" class="mt-2 space-y-3">
                <!-- Name -->
                <flux:input
                    wire:model="name"
                    :label="''"
                    type="text"
                    icon="user"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')"
                />

                <!-- Email Address -->
                <flux:input
                    wire:model="email"
                    :label="''"
                    type="email"
                    required
                    icon="envelope"
                    autocomplete="email"
                    placeholder="Email@clab.com.my"
                />

                <!-- Password -->
                <flux:input
                    wire:model="password"
                    :label="''"
                    type="password"
                    required
                    icon="lock-closed"
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable
                />

                <!-- Confirm Password -->
                <flux:input
                    wire:model="password_confirmation"
                    :label="''"
                    type="password"
                    required
                    icon="lock-closed"
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable
                />

                <div class="flex items-center justify-end">
                    <flux:button type="submit" variant="primary" class="w-full mb-4 mt-1">
                        {{ __('Create account') }}
                    </flux:button>
                </div>
            </form>

            <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Already have an account?') }}
                <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
            </div>
        </div>
    </div>    
</div>
