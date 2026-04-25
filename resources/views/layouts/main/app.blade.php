<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body
    class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900 flex flex-col">
    <div class="">
        <header class="demo-header" role="banner">
            <div class="demo-header-inner">
                <a href="{{ url('/') }}" class="brand" aria-label="Concept.LAB home">
                    <span class="brand-hex" aria-hidden="true">C</span>
                    <span class="brand-name">Concept.LAB</span>
                </a>

                <nav class="demo-nav" aria-label="Main navigation">
                    @guest
                        <a href="{{ route('login') }}" class="nav-link">Sign in</a>
                        <a href="{{ route('register') }}" class="nav-cta">Get started</a>
                    @endguest
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-cta">Dashboard →</a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Main content -->
        <main class="flex-1" role="main" id="main-content">
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="demo-footer" role="contentinfo">
            <p>ConceptLab &middot; Forensic Logic Auditor &middot; Grounded in SBA methodology</p>
        </footer>

    </div>
    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
