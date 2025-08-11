<div class="min-h-screen flex">
    <div class="hidden lg:block flex-1 bg-cover bg-center"
         style="background-image: url('{{ asset('image/background-ebookingclab-3.jpg') }}');">
    </div>

    <div class="w-full lg:w-2/5 bg-white flex items-center justify-center">
        <div class="w-full max-w-md p-10">

            <div class="text-center mb-8">
                <img src="{{ asset('image/logo-clab.png') }}" alt="CLAB Logo" class="mx-auto w-12 mb-3">
                <h1 class="text-3xl font-bold">e-BOOKING</h1>
                <p class="text-gray-600 text-sm">Optimizing Workplace Reservations</p>
            </div>

            <x-auth-session-status class="text-center mb-4" :status="session('status')" />

            <form wire:submit="login" class="space-y-5">
                <!-- CLAB ID -->
                <flux:input
                    wire:model="email"
                    type="email"
                    placeholder="Email"
                    icon="envelope"
                    required
                    autofocus
                    clearable
                    autocomplete="email"
                    :label="''"
                />

                <!-- Password -->
                <flux:input
                    wire:model="password"
                    type="password"
                    placeholder="Password"
                    icon="lock-closed"
                    required
                    viewable
                />               

                <!-- Options -->
                <div class="flex items-center justify-between text-sm">
                    <flux:checkbox wire:model="remember" label="Remember Me" class="text-gray-600 dark:text-gray-400"/>                    
                    @if (Route::has('password.request'))
                    <flux:link href="{{ route('password.request') }}" variant="subtle" class="text-sm">Forgot password?</flux:link>                  
                    @endif                    
                </div>

                <!-- Submit -->
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full mb-4">{{ __('Log in') }}</flux:button>
                </div>                
            </form>

            @if (Route::has('register'))
                <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
                    {{ __('Don\'t have an account?') }}
                    <flux:link :href="route('register')" wire:navigate>{{ __('Sign up') }}</flux:link>
                </div>
            @endif            
        </div>
    </div>
</div>
