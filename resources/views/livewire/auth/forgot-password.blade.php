<div class="min-h-screen flex">
    
    <!-- LEFT SIDE (Background Image) -->
    <div class="hidden lg:block flex-1 bg-cover bg-center"
         style="background-image: url('{{ asset('image/background-ebookingclab-3.jpg') }}');">
    </div>

    <!-- RIGHT SIDE (Login Form) -->
    <div class="w-full lg:w-2/5 flex items-center justify-center">
        <div class="w-full max-w-md p-10">

            <!-- Logo & Title -->
            <div class="text-center mb-8">
                <img src="{{ asset('image/logo-clab.png') }}" alt="CLAB Logo" class="mx-auto w-12 mb-3">
                <h1 class="text-3xl font-bold">e-BOOKING</h1>
                <p class="text-gray-600 dark:text-gray-300 text-sm">Optimizing Workplace Reservations</p>
            </div>

            <x-auth-header :title="''" :description="__('Enter your email to receive a password reset link')"/>

            <!-- Session Status -->
            <x-auth-session-status class="text-center mb-4" :status="session('status')" />

            <form wire:submit="sendPasswordResetLink" class="space-y-6">
                <!-- Email Address -->
                <flux:input
                    wire:model="email"
                    :label="''"
                    type="email"
                    icon="envelope"
                    required
                    autofocus
                    placeholder="Email"
                />

                <flux:button variant="primary" type="submit" class="w-full mb-4">{{ __('Email password reset link') }}</flux:button>
            </form>

            <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                {{ __('Don\'t have an account?') }}
                <flux:link :href="route('login')" wire:navigate>{{ __('log in') }}</flux:link>
            </div>
        </div>
    </div>                     
</div>
