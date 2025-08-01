<div>
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('View User') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('This page is for viewing user details') }}</flux:subheading>
        <flux:separator variant="subtle" />
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
