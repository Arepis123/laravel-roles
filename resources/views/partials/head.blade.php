<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? config('app.name') }}</title>

<link rel="icon" href="image/logo-clab.png" sizes="any">
<link rel="icon" href="image/logo-clab.png" type="image/png+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance

<style>
/* Hide scrollbar for sidebar while keeping scroll functionality */
[data-flux-sidebar] {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer 10+ */
}

[data-flux-sidebar]::-webkit-scrollbar {
    width: 0; /* WebKit browsers */
    background: transparent;
}

/* Alternative approach - hide scrollbar for any scrollable content in sidebar */
[data-flux-sidebar] * {
    scrollbar-width: none; /* Firefox */
    -ms-overflow-style: none; /* Internet Explorer 10+ */
}

[data-flux-sidebar] *::-webkit-scrollbar {
    width: 0; /* WebKit browsers */
    background: transparent;
}
</style>
