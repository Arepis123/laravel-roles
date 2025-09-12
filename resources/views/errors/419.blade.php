<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session Expired - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="max-w-xl mx-auto text-center">
            
            <!-- Illustration -->
            <div class="mb-0">
                <img src="{{ asset('image/time-vector.png') }}" alt="Session Expired" class="mx-auto max-w-6xl w-full">
            </div>

            <!-- Heading -->
            <flux:text class="text-2xl font-semibold text-black mb-4">Session is expired or invalid</flux:text>   

            <!-- Subtext -->
            <flux:text class="text-base mb-6">Your session has expired due to inactivity. Please log in again to continue.</flux:text>            

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <flux:button variant="primary" href="{{ route('login') }}">Relogin</flux:button>            
                <flux:button variant="filled" href="{{ route('password.request') }}">Reset my password</flux:button>
            </div>

        </div>
    </div>
</body>
</html>