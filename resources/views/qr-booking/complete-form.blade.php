<x-layouts.app>
    <div class="max-w-2xl mx-auto px-4 py-8">
        <flux:card class="p-6">
            <div class="text-center mb-6">
                <flux:heading size="xl">Complete Booking</flux:heading>
                <flux:subheading class="mt-2">Please provide completion details for your booking</flux:subheading>
            </div>

            <flux:card variant="filled" class="mb-6">
                <flux:heading size="base" class="mb-2">Booking Information</flux:heading>
                <div class="space-y-1 text-sm">
                    <p><span class="font-medium">Asset:</span> {{ $asset->getAssetDisplayName() }}</p>
                    <p><span class="font-medium">Booking #:</span> {{ $booking->id }}</p>
                    <p><span class="font-medium">Period:</span>
                        {{ $booking->start_time->format('M j, Y g:i A') }} -
                        {{ $booking->end_time->format('M j, Y g:i A') }}
                    </p>
                    <p><span class="font-medium">Purpose:</span> {{ $booking->purpose }}</p>
                </div>
            </flux:card>

            <!-- Display validation errors -->
            @if ($errors->any())
                <flux:card variant="danger" class="mb-6">
                    <flux:heading size="sm">Please correct the following errors:</flux:heading>
                    <ul class="mt-2 text-sm list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </flux:card>
            @endif

            <form action="{{ route('booking.process-completion', $booking) }}" method="POST" class="space-y-6">
                @csrf

                @if(class_basename($booking->asset_type) === 'Vehicle')
                    <!-- Vehicle specific fields -->
                    <flux:card variant="warning" class="mb-6">
                        <flux:heading size="sm">Required Information</flux:heading>
                        <flux:subheading class="mt-1">Please provide the current odometer reading before completing this vehicle booking.</flux:subheading>
                    </flux:card>

                    <div class="space-y-2">
                        <label for="odometer" class="block text-sm font-medium text-gray-900">
                            Current Odometer Reading <span class="text-red-500">*</span>
                        </label>
                        <input type="number"
                               id="odometer"
                               name="odometer"
                               step="0.1"
                               min="0"
                               value="{{ old('odometer') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('odometer') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="e.g., 12345.6"
                               required
                               autofocus>
                        @error('odometer')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @else
                            <p class="mt-2 text-sm text-gray-500">Enter the odometer reading as shown on the vehicle dashboard</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="gas_filled"
                               name="gas_filled"
                               type="checkbox"
                               value="1"
                               {{ old('gas_filled') ? 'checked' : '' }}
                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                        <label for="gas_filled" class="ml-3 text-sm font-medium text-gray-900">
                            Gas tank was filled
                        </label>
                    </div>

                    <div id="gas_amount_field" class="hidden space-y-2">
                        <label for="gas_amount" class="block text-sm font-medium text-gray-900">
                            Gas Amount (Liters)
                        </label>
                        <input type="number"
                               id="gas_amount"
                               name="gas_amount"
                               step="0.1"
                               min="0"
                               value="{{ old('gas_amount') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('gas_amount') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                               placeholder="Enter amount of gas filled">
                        @error('gas_amount')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <!-- Common remarks field for all asset types -->
                <div class="space-y-2">
                    <label for="remarks" class="block text-sm font-medium text-gray-900">
                        Additional Comments/Remarks
                    </label>
                    <textarea id="remarks"
                              name="remarks"
                              rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm @error('remarks') border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                              placeholder="Any additional comments or issues to report...">{{ old('remarks') }}</textarea>
                    @error('remarks')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-between pt-6">
                    <flux:button variant="ghost" href="{{ route('dashboard') }}">
                        Cancel
                    </flux:button>

                    <flux:button type="submit" variant="primary">
                        <flux:icon.check class="size-4" />
                        Complete Booking
                    </flux:button>
                </div>
            </form>
        </flux:card>
    </div>

    @if(class_basename($booking->asset_type) === 'Vehicle')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const gasFilled = document.getElementById('gas_filled');
                const gasAmountField = document.getElementById('gas_amount_field');

                function toggleGasAmount() {
                    if (gasFilled.checked) {
                        gasAmountField.classList.remove('hidden');
                    } else {
                        gasAmountField.classList.add('hidden');
                        document.getElementById('gas_amount').value = '';
                    }
                }

                gasFilled.addEventListener('change', toggleGasAmount);
                toggleGasAmount(); // Initial check
            });
        </script>
    @endif
</x-layouts.app>