<div>
    <div class="relative mb-6 w-full">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('View User') }}</h1>
        <p class="text-gray-600 mt-1 dark:text-gray-400">{{ __('This page is for viewing user details') }}</p>
        <flux:separator variant="subtle" class="mt-4" />        
    </div>

    <div class="py-3">
        <flux:button variant="primary" href="{{ route('users.index') }}">Back</flux:button>
    </div>

    <div>
        <form  wire:submit="submit" class="">
            <div class="py-3">
                <div class="mb-4">
                    <flux:input type="name" label="Name" value="{{ $user->name }}" readonly/>
                </div>
                <div class="mb-4">
                    <flux:input type="email" label="Email"  value="{{ $user->email }}" readonly/>
                </div>
            </div>
        </form>
    </div>


</div>
