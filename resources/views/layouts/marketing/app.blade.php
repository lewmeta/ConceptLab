<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ $title ?? 'Conceptlabs — Defend Your Definitions' }}</title>
    <meta name="description"
        content="{{ $description ?? 'A precision engine for strategists. Detect structural failures in definitions, claims, and strategy briefs — then earn the repair.' }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..900;1,9..144,300..900&family=Instrument+Sans:wght@400;500;600&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="bg-cream font-sans text-navy overflow-x-hidden">

    <!-- Marketing Nav -->
    <nav>Navigation</nav>

    <!-- Main -->
    <main>
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer>Footer</footer>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
