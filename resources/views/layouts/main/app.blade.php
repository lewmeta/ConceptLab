<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen flex flex-col bg-base text-white antialiased">

    <!-- Header -->
    <header class="sticky top-0 z-50 border-b border-white/6 backdrop-blur-md">
        <div class="mx-auto flex h-14 max-w-6xl items-center justify-between px-6">

            <!-- Brand -->
            <a href="{{ url('/') }}" class="flex items-center gap-2.5 no-underline">
                <div
                    class="brand-hex flex h-6 w-6 items-center justify-center bg-accent text-[11px] font-medium text-white">
                    S
                </div>
                <span class="text-sm font-medium tracking-tight text-white">
                    Conceptlab
                </span>
            </a>

            <!-- Nav -->
            <nav class="flex items-center gap-3">
                @guest
                    <flux:button href="{{ route('login') }}" variant="ghost" size="sm">
                        Sign in
                    </flux:button>
                    <flux:button href="{{ route('register') }}" variant="filled" size="sm">
                        Get started
                    </flux:button>
                @endguest
                @auth
                    <flux:button href="{{ route('dashboard') }}" variant="filled" size="sm">
                        Dashboard
                    </flux:button>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Main -->
    <main class="flex-1 max-w-6xl mx-auto w-full">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/6 px-6 py-5 text-center text-xs text-white/30">
        ConceptLab &middot; Forensic Logic Auditor &middot; Grounded in SBA methodology
    </footer>

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist
    @fluxScripts
    @livewireScripts
    @stack('scripts')

</body>

</html>
