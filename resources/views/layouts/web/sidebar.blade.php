<!-- Large -->
<div aria-expanded="true"
    class="hidden lg:block fixed h-full left-0 z-41! cursor-e-resize aria-expanded:cursor-default transition-[width] duration-150 backdrop-blur-md border-r text-[#0f0f10] group/sidebar bg-gray-50"
    :class="{ 'w-[16rem]': show, 'w-15.25': !show }" x-data="{ show: true }" @colapse-toggle.window="show = false;"
    @open-toggle.window="show = true;">
    <div id="sidebar-portal-root"></div>
    <div class="relative flex flex-col w-full h-full pb-0 overflow-hidden scroll-smooth max-h-screen">
        <!-- Logo -->
        <div class="flex flex-row justify-between items-center px-3 w-full relative z-20">
            <div class="flex justify-between relative group/header-logo items-center h-12.5 w-full">
                <div class="flex duration-100 w-full">
                    <div class="flex flex-col items-center transition-transform duration-150"
                        :class="{ 'translate-x-3.25': show, 'translate-x-0': !show }">

                        <div class="flex items-center min-h-4.5 gap-2"
                            :class="{ 'justify-start w-[109.125px]': show, 'justify-center w-10.25': !show }">
                            <img src="{{ asset('favicon.svg') }}" class="size-7 object-contain " alt="">
                            <h1 class="font-waldenburg font-bold text-[18.5px]" x-show="show"> {{ __('ConceptLabs') }}
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex h-full flex-1 flex-col grow min-h-0" id="main-nav">
            <div class="px-3.5 w-full flex flex-col items-center relative z-20 mt-2"
                :class="{ 'items-start justify-start': show, 'items-center justify-center': !show }">
                <div class="flex items-center relative w-full">
                    <button class="group/main-nav-item rounded-[10px] bg-white -mx-0.5"
                        aria-label="Platform switcher. Click to switch platforms." type="button" id="radix-_r_298_"
                        aria-haspopup="menu" aria-expanded="false" data-state="closed"
                        style="box-shadow: rgba(0, 0, 0, 0.04) 0px 2px 4px 0px, rgba(0, 0, 0, 0.4) 0px 0px 1.07px 0px;">
                        <div class="relative group rounded-[10px] overflow-hidden bg-transparent transition-all duration-0 hover:transition-all hover:duration-0 text-gray-950 ring-1 ring-inset ring-white hover:ring-white"
                            :class="{ 'w-57.75': show, 'w-9.25 overflow-hidden': !show }" data-state="closed">
                            <div class="flex items-center gap-2 text-gray-500 px-2" {{-- :class="{ 'min-w-36': show, 'w-10.25 overflow-hidden': !show}" --}}>
                                <div class="flex items-center justify-center h-8">
                                    <div
                                        class="w-5 h-5 flex items-center justify-center rounded-md relative bg-gray-100 border">
                                        <div class="relative z-20">
                                            <img alt="ElevenCreative" class="w-3 h-3 bg-gray-100 rounded-full"
                                                src="{{ asset('profile/profile.webp') }}">
                                        </div>
                                    </div>
                                </div>

                                <div x-show="show"
                                    class="flex items-center justify-between flex-1 transition-all duration-150 group-aria-expanded/sidebar:opacity-100 opacity-0 translate-x-1 group-aria-expanded/sidebar:translate-x-0 h-9"
                                    x-show="show">
                                    <p
                                        class="text-[13px] font-normal whitespace-nowrap max-w-42 truncate text-gray-900">
                                        Lewis' workspace is coming along good.
                                    </p>
                                    <div class="flex items-center ml-auto mr-0.5 justify-center w-4 h-4">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="lucide lucide-chevrons-up-down w-4 h-4">
                                            <path d="m7 15 5 5 5-5"></path>
                                            <path d="m7 9 5-5 5 5"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <div class="flex h-full flex-1 flex-col grow min-h-0 overflow-y-auto no-scrollbar overflow-x-hidden"
                style="mask: linear-gradient(rgba(255, 255, 255, 0), rgb(255, 255, 255) 8px, rgb(255, 255, 255) 100%);">
                <div class="relative flex flex-col grow w-full shrink-0">
                    <div class="relative flex flex-col overflow-visible grow w-full">
                        <ul class="w-full flex flex-col gap-2 lg:gap-0 shrink-0 p-3"
                            :class="{ 'items-start': show, 'items-center': !show }">
                            <li class="w-full">
                                <div class="relative w-full flex cursor-default select-none hover:bg-neutral-100 items-center rounded-lg px-2 py-1.5 text-sm outline-none transition-colors data-disabled:pointer-events-none data-disabled:opacity-50"
                                    :class="{ 'flex items-start': show, 'items-center': show }">
                                    <svg class="mr-2 w-4 h-4" viewBox="0 0 20 20" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" color="currentColor">
                                        <path
                                            d="M8.31842 2.82462L8.79137 3.4067L8.31842 2.82462ZM9.55075 2.00081L9.34859 1.27857L9.55075 2.00081ZM11.6816 2.82462L12.1545 2.24253L11.6816 2.82462ZM10.4493 2.00081L10.6514 1.27857L10.4493 2.00081ZM4.24335 16.485L4.58384 15.8168L4.24335 16.485ZM3.51499 15.7566L4.18325 15.4162L3.51499 15.7566ZM16.485 15.7566L15.8168 15.4162L16.485 15.7566ZM15.7567 16.485L15.4162 15.8168L15.7567 16.485ZM16.6078 7.22929L15.8843 7.42684L16.6078 7.22929ZM15.6816 6.07462L15.2086 6.6567L15.6816 6.07462ZM16.3546 6.69727L16.9642 6.26031L16.3546 6.69727ZM3.39219 7.22929L4.11571 7.42684L3.39219 7.22929ZM4.31842 6.07462L3.84548 5.49253L4.31842 6.07462ZM3.64542 6.69727L3.03585 6.26031L3.64542 6.69727ZM6.66667 12.5833C6.25246 12.5833 5.91667 12.9191 5.91667 13.3333C5.91667 13.7475 6.25246 14.0833 6.66667 14.0833V12.5833ZM13.3333 14.0833C13.7475 14.0833 14.0833 13.7475 14.0833 13.3333C14.0833 12.9191 13.7475 12.5833 13.3333 12.5833V14.0833ZM16.6667 8.14425H15.9167V14H16.6667H17.4167V8.14425H16.6667ZM14 16.6667V15.9167H6V16.6667V17.4167H14V16.6667ZM3.33334 14H4.08334V8.14425H3.33334H2.58334V14H3.33334ZM4.31842 6.07462L4.79137 6.6567L8.79137 3.4067L8.31842 2.82462L7.84548 2.24253L3.84548 5.49253L4.31842 6.07462ZM11.6816 2.82462L11.2086 3.4067L15.2086 6.6567L15.6816 6.07462L16.1545 5.49253L12.1545 2.24253L11.6816 2.82462ZM8.31842 2.82462L8.79137 3.4067C9.09961 3.15626 9.29964 2.99434 9.46314 2.88044C9.61888 2.77194 9.69878 2.7382 9.75291 2.72305L9.55075 2.00081L9.34859 1.27857C9.06953 1.35668 8.83297 1.49133 8.6057 1.64967C8.38619 1.80259 8.13666 2.00595 7.84548 2.24253L8.31842 2.82462ZM11.6816 2.82462L12.1545 2.24253C11.8633 2.00595 11.6138 1.80259 11.3943 1.64967C11.167 1.49133 10.9305 1.35668 10.6514 1.27857L10.4493 2.00081L10.2471 2.72305C10.3012 2.7382 10.3811 2.77194 10.5369 2.88044C10.7004 2.99434 10.9004 3.15626 11.2086 3.4067L11.6816 2.82462ZM9.55075 2.00081L9.75291 2.72305C9.91453 2.67781 10.0855 2.67781 10.2471 2.72305L10.4493 2.00081L10.6514 1.27857C10.2253 1.1593 9.77468 1.1593 9.34859 1.27857L9.55075 2.00081ZM6 16.6667V15.9167C5.52092 15.9167 5.20671 15.9161 4.96641 15.8965C4.73487 15.8775 4.63875 15.8447 4.58384 15.8168L4.24335 16.485L3.90286 17.1533C4.20447 17.3069 4.51996 17.365 4.84427 17.3915C5.15981 17.4172 5.54567 17.4167 6 17.4167V16.6667ZM3.33334 14H2.58334C2.58334 14.4543 2.58275 14.8402 2.60853 15.1557C2.63503 15.48 2.69306 15.7955 2.84674 16.0971L3.51499 15.7566L4.18325 15.4162C4.15527 15.3612 4.12247 15.2651 4.10355 15.0336C4.08392 14.7933 4.08334 14.4791 4.08334 14H3.33334ZM4.24335 16.485L4.58384 15.8168C4.41136 15.7289 4.27113 15.5886 4.18325 15.4162L3.51499 15.7566L2.84674 16.0971C3.07843 16.5519 3.44813 16.9216 3.90286 17.1533L4.24335 16.485ZM16.6667 14H15.9167C15.9167 14.4791 15.9161 14.7933 15.8965 15.0336C15.8775 15.2651 15.8447 15.3612 15.8168 15.4162L16.485 15.7566L17.1533 16.0971C17.3069 15.7955 17.365 15.48 17.3915 15.1557C17.4173 14.8402 17.4167 14.4543 17.4167 14H16.6667ZM14 16.6667V17.4167C14.4543 17.4167 14.8402 17.4172 15.1557 17.3915C15.48 17.365 15.7955 17.3069 16.0971 17.1533L15.7567 16.485L15.4162 15.8168C15.3613 15.8447 15.2651 15.8775 15.0336 15.8965C14.7933 15.9161 14.4791 15.9167 14 15.9167V16.6667ZM16.485 15.7566L15.8168 15.4162C15.7289 15.5886 15.5886 15.7289 15.4162 15.8168L15.7567 16.485L16.0971 17.1533C16.5519 16.9216 16.9216 16.5519 17.1533 16.0971L16.485 15.7566ZM16.6667 8.14425H17.4167C17.4167 7.71926 17.4229 7.36702 17.3313 7.03174L16.6078 7.22929L15.8843 7.42684C15.9105 7.52266 15.9167 7.6367 15.9167 8.14425H16.6667ZM15.6816 6.07462L15.2086 6.6567C15.6026 6.97677 15.6872 7.05349 15.745 7.13422L16.3546 6.69727L16.9642 6.26031C16.7617 5.97784 16.4844 5.76053 16.1545 5.49253L15.6816 6.07462ZM16.6078 7.22929L17.3313 7.03174C17.2557 6.75483 17.1314 6.49361 16.9642 6.26031L16.3546 6.69727L15.745 7.13422C15.8085 7.22272 15.8556 7.3218 15.8843 7.42684L16.6078 7.22929ZM3.33334 8.14425H4.08334C4.08334 7.6367 4.08954 7.52266 4.11571 7.42684L3.39219 7.22929L2.66868 7.03174C2.57713 7.36702 2.58334 7.71926 2.58334 8.14425H3.33334ZM4.31842 6.07462L3.84548 5.49253C3.51563 5.76053 3.23834 5.97784 3.03585 6.26031L3.64542 6.69727L4.25498 7.13422C4.31285 7.05349 4.39744 6.97676 4.79137 6.6567L4.31842 6.07462ZM3.39219 7.22929L4.11571 7.42684C4.14438 7.3218 4.19155 7.22272 4.25498 7.13422L3.64542 6.69727L3.03585 6.26031C2.86862 6.49361 2.74428 6.75483 2.66868 7.03174L3.39219 7.22929ZM6.66667 13.3333V14.0833H13.3333V13.3333V12.5833H6.66667V13.3333Z"
                                            fill="currentColor"></path>
                                    </svg>
                                    <span x-show="show">Dashboard</span>
                                </div>
                            </li>
                            <li class="w-full">
                                <div class="relative w-full flex cursor-default select-none hover:bg-neutral-100 items-center rounded-lg px-2 py-1.5 text-sm outline-none transition-colors data-disabled:pointer-events-none data-disabled:opacity-50"
                                    :class="{ 'flex items-start': show, 'items-center': show }">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mr-2 w-4 h-4">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <span x-show="show">Team</span>
                                </div>
                            </li>
                            <li class="w-full">
                                <div class="relative flex cursor-default select-none items-center rounded-lg px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-disabled:pointer-events-none data-disabled:opacity-50"
                                    :class="{ 'flex items-start': show, 'items-center': show }">
                                    <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                        </path>
                                    </svg>
                                    <span x-show="show">Forum</span>
                                </div>
                            </li>

                            <li aria-label="Monitor" class="mt-4 w-full">
                                <h2 x-show="show"
                                    class="text-sm font-normal text-black/60 ml-1.5 mb-1.5 whitespace-nowrap group-aria-expanded/sidebar:opacity-100 opacity-0 transition-opacity duration-150">
                                    Monitor
                                </h2>
                                <ul class="w-full flex flex-col gap-2 lg:gap-0 shrink-0"
                                    :class="{ 'items-start': show, 'items-center': !show }">
                                    <li class="w-full">
                                        <div class="relative w-full flex cursor-default select-none items-center rounded-lg px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-disabled:pointer-events-none data-disabled:opacity-50"
                                            :class="{ 'flex items-start': show, 'items-center': show }">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.25rem" height="1.25rem"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="mr-2 w-4 h-4">
                                                <path d="M3 3v16a2 2 0 0 0 2 2h16"></path>
                                                <path d="M18 17V9"></path>
                                                <path d="M13 17V5"></path>
                                                <path d="M8 17v-3"></path>
                                            </svg>
                                            <span x-show="show">Analytics</span>
                                        </div>
                                    </li>

                                    <li class="w-full">
                                        <div class="relative flex cursor-default select-none items-center rounded-lg px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-disabled:pointer-events-none data-disabled:opacity-50"
                                            :class="{ 'flex items-start': show, 'items-center': show }">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="1.25rem" height="1.25rem"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="mr-2 w-4 h-4">
                                                <path d="M16 22h2a2 2 0 0 0 2-2V7l-5-5H6a2 2 0 0 0-2 2v3">
                                                </path>
                                                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                                <circle cx="8" cy="16" r="6"></circle>
                                                <path d="M9.5 17.5 8 16.25V14"></path>
                                            </svg>
                                            <span x-show="show">Audit</span>
                                        </div>
                                    </li>

                                </ul>
                            </li>


                            {{-- <flux:navbar.item icon="layout-grid" :href="route('dashboard')"
                                :current="request()->routeIs('dashboard')" wire:navigate x-show="show"
                                class="[&>div>svg]:size-4 w-full! font-light!">
                                {{ __('Dashboard') }}
                            </flux:navbar.item>

                            <flux:navbar.item class="[&>div>svg]:size-4 w-full" icon="magnifying-glass"
                                href="#" x-show="show">
                                {{ __('Search') }}
                            </flux:navbar.item>

                            <flux:tooltip :content="__('Search')" position="right" x-show="!show">
                                <flux:navbar.item class="[&>div>svg]:size-4" icon="magnifying-glass" href="#">
                                </flux:navbar.item>
                            </flux:tooltip>

                            <flux:tooltip :content="__('Dashboard')" position="right" x-show="!show">
                                <flux:navbar.item icon="layout-grid" :href="route('dashboard')"
                                    :current="request()->routeIs('dashboard')" wire:navigate
                                    class="[&>div>svg]:size-4">
                                </flux:navbar.item>
                            </flux:tooltip> --}}
                        </ul>
                    </div>
                </div>

                <!-- Foot -->
                <div class="flex flex-col mt-1 pb-3">
                    <div class="px-3 mb-1">
                        <div class="relative">
                            <ul class="flex flex-col gap-1 cursor-default">
                                <div style="opacity: 1;"></div>
                                <div class="group/card relative" style="opacity: 1; transform: none;" x-show="show">
                                    <div class="group/cardbox group/card relative overflow-hidden bg-background rounded-xl mb-0.5 shadow-[0px_0px_1.072px_0px_rgba(0,0,0,0.4),0px_2px_4px_0px_rgba(0,0,0,0.04)] dark:border dark:border-gray-200"
                                        :class="{ 'w-57.75': show, 'w-10.25': !show }">
                                        <div style="opacity: 1;">
                                            <div class="flex items-center justify-between px-3.5 py-2.5">
                                                <p class="text-xs font-medium text-gray-500" x-show="show">Complete
                                                    to earn credits
                                                </p>
                                                <svg width="16" height="16" viewBox="0 0 20 20"
                                                    role="progressbar" aria-valuenow="0" aria-valuemin="0"
                                                    aria-valuemax="100" class="-rotate-90">
                                                    <circle cx="10" cy="10" r="8" fill="none"
                                                        stroke="currentColor" stroke-width="2" class="text-gray-100">
                                                    </circle>
                                                    <circle cx="10" cy="10" r="8" fill="none"
                                                        stroke="currentColor" stroke-width="2"
                                                        class="text-foreground transition-all"
                                                        stroke-dasharray="50.26548245743669"
                                                        stroke-dashoffset="50.26548245743669" stroke-linecap="round"
                                                        style="transition-duration: 0ms;"></circle>
                                                </svg>
                                            </div>
                                            <div class="relative overflow-hidden" style="height: 86px;"
                                                x-show="show">
                                                <div style="opacity: 1;">
                                                    <a class="focus-ring block px-3.5 py-3 border-t border-gray-200 rounded-b-xl"
                                                        href="/app/speech-synthesis/text-to-speech">
                                                        <div class="flex flex-col gap-0.5">
                                                            <p class="text-sm text-foreground font-medium text-pretty">
                                                                Generate speech from text</p>
                                                            <p
                                                                class="font-normal text-[13px] text-gray-500 text-pretty">
                                                                Pick a voice, then try generating speech.</p>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button aria-label="Dismiss"
                                        class="inline-flex items-center justify-center whitespace-nowrap font-medium focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! text-foreground radix-state-open:text-foreground radix-state-open:bg-gray-200 radix-state-on:text-foreground radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 text-xs center absolute -top-2 -right-2 w-5 h-5 p-1.5 opacity-0 group-hover/card:opacity-100 transition-opacity duration-200 bg-background hover:bg-gray-100 rounded-full shadow-natural-xs"
                                        x-show="show">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20px" height="20px"
                                            viewBox="0 0 16 16" fill="none" class="shrink-0 w-3 h-3">
                                            <path d="M4 4L12 12M12 4L4 12" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round"></path>
                                        </svg>
                                    </button>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <div class="px-3.5 w-full pb-1 pt-0.5">
                        <a class="relative w-full overflow-hidden group/upgrade z-1 p-0 isolate rounded-lg text-sm font-medium justify-center flex items-center gap-2 px-1.5 h-8 focus-visible:ring-2 focus-visible:outline-none ring-gray-950"
                            aria-label="Upgrade subscription group" data-state="closed" href="/app/subscription">
                            <div
                                class="absolute ring-1 ring-inset group-hover/upgrade:ring-gray-300 ring-gray-200 rounded-lg z-10 inset-0 pointer-events-none">
                            </div>
                            <div class="absolute inset-0 z-1"
                                style="background-image: repeating-linear-gradient(135deg, #ffffff, #fff 4px, #0000170b 4px, #0000170b 8px);">
                            </div>
                            <div class="absolute z-1 inset-0 group-hover:bg-gray-200!"
                                style="background-image: linear-gradient(90deg, #fff, #ffffff00);"></div>
                            <div
                                class="flex z-2 items-center gap-2 translate-x-8.25 group-aria-expanded/sidebar:translate-x-0 transition-transform duration-150">
                                <div class="w-5 h-5 flex items-center justify-center">
                                    <svg width="18px" height="18px" viewBox="0 0 16 16" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" class="relative">
                                        <rect width="16" height="16" rx="8"
                                            fill="url(#paint0_linear_1_1999)"></rect>
                                        <path
                                            d="M8.66725 4.80043C8.66725 4.30571 8.02563 4.11145 7.75121 4.52307L5.0626 8.55599C4.84108 8.88827 5.07928 9.33335 5.47863 9.33335H7.3339V11.1996C7.3339 11.6943 7.97552 11.8886 8.24994 11.4769L10.9385 7.44401C11.1601 7.11173 10.9219 6.66665 10.5225 6.66665H8.66725V4.80043Z"
                                            fill="white"></path>
                                        <defs>
                                            <linearGradient id="paint0_linear_1_1999" x1="8" y1="0"
                                                x2="8" y2="16" gradientUnits="userSpaceOnUse">
                                                <stop stop-color="#6B7280"></stop>
                                                <stop offset="1"></stop>
                                            </linearGradient>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="relative flex flex-col transition-all duration-150 translate-x-1"
                                    x-show="show">
                                    Upgrade
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</div>

<!-- Small -->
<div class="relative z-50 lg:hidden group/sidebar" role="dialog" x-data="{ open: false }"
    @keydown.escape.window="open = false" @sb02-toggle.window="open = true;">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-gray-900/30 transition-opacity ease-linear duration-300 opacity-100 pointer-events-auto"
        x-show="open"></div>

    <div class="fixed inset-0 flex pointer-events-none" x-show="open">
        <div
            class="relative mr-16 flex w-64 transition-transform ease-in-out duration-300 translate-x-0 pointer-events-auto">
            <div class="flex grow flex-col overflow-y-auto bg-white overflow-x-hidden">
                <!-- Logo --->
                <div class="flex justify-between pr-3.5 items-center w-full shrink-0 h-12.5">
                    <div class="flex flex-row justify-between items-center px-3 w-full relative z-20">
                        <div class="flex justify-between relative group/header-logo items-center h-12.5 w-full">
                            <div class="flex opacity-100 transition-opacity duration-100">
                                <div class="flex items-center translate-x-3.25 transition-transform duration-150">
                                    <div class="flex items-center h-4.5 w-[109.125px]">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1510 248"
                                            class="w-full h-full text-black fill-black -left-px relative will-change-auto">
                                            <g style="transform: translateX(78px) translateY(124px) scale(0.75); transform-origin: 49.0125px 0px;"
                                                transform-origin="49.01249694824219px 0px">
                                                <g fill="#000" fill-rule="evenodd">
                                                    <path d="M0.25,248.085 L52.252,248.085 L52.252,0.25 L0.25,0.25 Z"
                                                        transform="translate(-78, -124)"></path>
                                                    <path d="M0.25,248.085 L52.252,248.085 L52.252,0.25 L0.25,0.25 Z"
                                                        transform="translate(20, -124)"></path>
                                                </g>
                                                <g fill="#000" fill-rule="evenodd">
                                                    <g opacity="1"
                                                        style="transform: translateX(128.81px); transform-origin: 0.0439987px 0px;"
                                                        transform-origin="0.04399871826171875px 0px">
                                                        <path
                                                            d="M-76.956,-123.918 L-76.956,123.918 L76.956,123.918 L76.956,82.612 L-24.954,82.612 L-24.954,17.702 L69.976,17.702 L69.976,-23.604 L-24.954,-23.604 L-24.954,-82.613 L76.956,-82.613 L76.956,-123.918 Z"
                                                            transform="translate(77, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(314.12px); transform-origin: 0.395px 0px;"
                                                        transform-origin="0.3950004577636719px 0px">
                                                        <path
                                                            d="M-24.605,123.918 L24.604,123.918 L24.604,-123.918 L-24.605,-123.918 Z"
                                                            transform="translate(25, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(393.03px) translateY(31.07px); transform-origin: 0.363998px 0px;"
                                                        transform-origin="0.3639984130859375px 0px">
                                                        <path
                                                            d="M36.115,-20.658 C33.671,-54.695 21.807,-65.807 1.222,-65.807 C-19.362,-65.807 -32.274,-54.335 -35.761,-20.658 Z M-84.636,-0.173 C-84.636,-67.910 -50.753,-97.016 1.919,-97.016 C54.583,-97.016 84.636,-68.212 84.636,0.522 L84.636,11.632 L-36.112,11.632 C-34.369,51.904 -22.155,65.455 1.222,65.455 C19.711,65.455 31.224,54.682 33.318,35.929 L82.540,35.929 C79.399,76.912 45.196,97.016 1.222,97.016 C-54.603,97.016 -84.636,67.528 -84.636,-0.173 Z"
                                                            transform="translate(85, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(572.9px) translateY(31.07px); transform-origin: 0.224998px 0px;"
                                                        transform-origin="0.22499847412109375px 0px">
                                                        <path
                                                            d="M-37.518,-92.852 L-87.775,-92.852 L-27.048,92.852 L27.048,92.852 L87.775,-92.852 L37.518,-92.852 L-0.509,47.033 Z"
                                                            transform="translate(88, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(757.74px) translateY(31.07px); transform-origin: 0.363998px 0px;"
                                                        transform-origin="0.3639984130859375px 0px">
                                                        <path
                                                            d="M36.115,-20.658 C33.671,-54.695 21.807,-65.807 1.222,-65.807 C-19.362,-65.807 -32.274,-54.335 -35.761,-20.658 Z M-84.636,-0.173 C-84.636,-67.910 -50.753,-97.016 1.919,-97.016 C54.583,-97.016 84.636,-68.212 84.636,0.522 L84.636,11.632 L-36.112,11.632 C-34.369,51.904 -22.155,65.455 1.222,65.455 C19.711,65.455 31.224,54.682 33.318,35.929 L82.540,35.929 C79.399,76.912 45.196,97.016 1.222,97.016 C-54.603,97.016 -84.636,67.528 -84.636,-0.173 Z"
                                                            transform="translate(85, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(956.36px) translateY(28.99px); transform-origin: 0.68px 0px;"
                                                        transform-origin="0.6800003051757812px 0px">
                                                        <path
                                                            d="M-31.41,-14.405 C-31.41,-43.562 -17.45,-59.529 4.19,-59.529 C21.99,-59.529 32.11,-43.562 32.11,-24.471 L32.11,94.934 L81.32,94.934 L81.32,-32.108 C81.32,-75.149 57.24,-94.934 21.29,-94.934 C-2.79,-94.934 -22.69,-79.117 -31.41,-66.818 L-31.41,-90.769 L-81.32,-90.769 L-81.32,94.934 L-31.41,94.934 Z"
                                                            transform="translate(82, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(1160.36px); transform-origin: 0.135002px 0px;"
                                                        transform-origin="0.13500213623046875px 0px">
                                                        <path
                                                            d="M-74.865,-123.918 L-22.855,-123.918 L-22.855,82.612 L74.865,82.612 L74.865,123.918 L-74.865,123.918 Z"
                                                            transform="translate(75, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(1321.99px) translateY(31.07px); transform-origin: 0.444992px 0.00050354px;"
                                                        transform-origin="0.4449920654296875px 0.0005035400390625px">
                                                        <path
                                                            d="M1.395,-60.57 C25.825,-60.57 39.095,-38.37 39.095,-0.173 C39.095,41.827 25.825,60.571 1.395,60.571 C-23.035,60.571 -36.995,41.827 -36.995,-0.173 C-36.995,-42.173 -23.035,-60.57 1.395,-60.57 Z M-86.555,-0.173 C-86.555,68.554 -49.56,97.017 -12.915,97.017 C5.583,97.017 30.365,82.439 37.695,70.984 L37.695,92.852 L86.555,92.852 L86.555,-92.851 L36.295,-92.851 L36.295,-72.719 C28.625,-85.215 9.435,-97.016 -11.515,-97.016 C-53.745,-97.016 -86.555,-69.243 -86.555,-0.173 Z"
                                                            transform="translate(87, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(1532.62px) translateY(2.09px); transform-origin: 0.970001px 0px;"
                                                        transform-origin="0.970001220703125px 0px">
                                                        <path
                                                            d="M-1.92,-31.24 C22.51,-31.24 36.47,-2.43 36.47,28.81 C36.47,70.81 22.51,89.554 -1.92,89.554 C-26.35,89.554 -39.61,70.81 -39.61,28.81 C-39.61,-13.19 -26.35,-31.24 -1.92,-31.24 Z M-37.17,99.967 C-30.19,114.545 -7.5,126 11,126 C53.92,126 86.03,83.81 86.03,28.81 C86.03,-42 65.09,-68.033 12.04,-68.033 C-8.9,-68.033 -28.44,-56.579 -36.82,-43.736 L-36.82,-126 L-86.03,-126 L-86.03,121.835 L-37.17,121.835 Z"
                                                            transform="translate(87, 0)"></path>
                                                    </g>
                                                    <g opacity="1"
                                                        style="transform: translateX(1725.07px) translateY(31.07px); transform-origin: 0.824997px 1.52588e-05px;"
                                                        transform-origin="0.8249969482421875px 0.0000152587890625px">
                                                        <path
                                                            d="M-78.175,37.661 L-28.975,37.661 C-28.275,57.099 -17.805,66.471 0.695,66.471 C19.195,66.471 29.665,58.14 29.665,43.561 C29.665,30.371 21.635,25.512 4.185,21.347 L-10.825,17.529 C-53.405,6.769 -74.685,-4.685 -74.685,-39.743 C-74.685,-74.801 -41.885,-97.016 -0.005,-97.016 C41.875,-97.016 73.635,-80.702 75.035,-42.173 L25.825,-42.173 C24.775,-59.181 14.305,-66.471 -0.705,-66.471 C-15.705,-66.471 -26.175,-59.182 -26.175,-45.298 C-26.175,-32.455 -17.805,-27.596 -3.145,-24.124 L12.215,-20.305 C52.695,-10.239 78.175,-0.174 78.175,37.314 C78.175,74.801 44.675,97.016 -0.705,97.016 C-49.915,97.016 -77.135,78.619 -78.175,37.661 Z"
                                                            transform="translate(79, 0)">
                                                        </path>
                                                    </g>
                                                </g>
                                            </g>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Toggle button -->
                    <button data-state="closed" data-agent-id="button-_r_1nv_" @click="open = false"
                        class="relative inline-flex items-center justify-center whitespace-nowrap text-sm font-medium focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-transparent hover:bg-gray-100 radix-state-open:text-[#0f0f10] radix-state-open:bg-gray-200 radix-state-on:text-[#0f0f10] radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 rounded-[10px] pointer-events-auto p-0 h-8 w-8 text-gray-500 hover:text-gray-950 duration-100 transition-colors shrink-0 cursor-w-resize">
                        <svg width="20px" height="20px" viewBox="0 0 20 20" fill="none"
                            xmlns="http://www.w3.org/2000/svg" color="currentColor" class="w-5 h-5">
                            <rect x="7" y="6.5" width="7" height="1.5" rx="0.75"
                                transform="rotate(90 7 6.5)" fill="currentColor"></rect>
                            <rect x="3" y="4" width="14" height="12" rx="2.8" stroke="currentColor"
                                stroke-width="1.5"></rect>
                        </svg>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
