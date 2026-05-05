<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
</head>

<body class="bg-white">

    <!-- Sidebar -->
    @include('layouts.web.sidebar')

    <!-- Body Layout -->
    <div id="content"
        class="z-10! max-lg:pl-0! flex h-full flex-1 flex-col lg:min-h-dvh pl-64 transition-[padding] duration-150">
        <!-- Header -->
        @include('layouts.web.header')

        <!-- Body content -->
        <div
            class="stack min-h-[calc(100%-7.9px)] pt-12.5 box-border max-[1023px]:pt-[3.12rem] max-[1023px]:pb-0 lg:transition-[padding_200ms_cubic-bezier(0.42,0,0.58,1)] lg:w-full lg:mx-auto lg:pb-0 px-5">
            <div class="relative w-full max-h-22 min-h-5 overflow-hidden">
                <div class="relative w-full pb-0 xl:pb-[calc(50%-576px)]">
                </div>
            </div>

            <!-- Main -->
            <main class="relative bg-white flex-[1_1_0] mx-auto w-full">
                <div class="h-full w-full">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @fluxScripts
</body>

</html>
