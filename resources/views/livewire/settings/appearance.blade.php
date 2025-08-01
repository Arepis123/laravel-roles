<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Appearance')" :subheading=" __('Update the appearance settings for your account')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Light') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Dark') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('System') }}</flux:radio>
        </flux:radio.group>

        <!-- <div class="my-3">
            <flux:heading size="md" class="mb-6">{{ __('Font') }}</flux:heading>
        </div>
        
        <flux:radio.group x-data variant="segmented" wire:model="fontSize">
            <flux:radio value="sm" icon="arrows-pointing-in">{{ __('Small') }}</flux:radio>
            <flux:radio value="base" icon="arrows-right-left">{{ __('Medium') }}</flux:radio>
            <flux:radio value="lg" icon="arrows-pointing-out">{{ __('Large') }}</flux:radio>
        </flux:radio.group>
         -->
    </x-settings.layout>
</section>
