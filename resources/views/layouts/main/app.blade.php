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
            @if (Route::has('login'))
                <nav class="flex items-center gap-3">
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="filled" size="sm">
                            Dashboard
                        </flux:button>
                    @else
                        <flux:button href="{{ route('login') }}" variant="ghost" size="sm">
                            Sign in
                        </flux:button>
                        @if (Route::has('register'))
                            <flux:button href="{{ route('register') }}" variant="filled" size="sm">
                                Get started
                            </flux:button>
                        @endif

                    @endauth
                </nav>
            @endif

            {{-- @if (Route::has('login'))
                <nav class="flex items-center justify-end gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                            class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal">
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                                class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif --}}
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
