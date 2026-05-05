<header class="lg:hidden fixed top-0 h-[3.2rem] w-full bg-[#ffffffe6] backdrop-blur-sm stack z-30">
    <div class="w-full flex flex-row px-2.5 h-12.5 border-b">
        <!-- Left action -->
        <div class="h-12.5 flex flex-row items-center gap-2">
            <!-- Open sidebar -->
            <button aria-label="Open Sidebar" data-agent-id="button-_r_1ob_"
                class="relative inline-flex items-center justify-center whitespace-nowrap text-sm font-medium focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-transparent hover:bg-gray-100 radix-state-open:text-[#0f0f10] radix-state-open:bg-gray-200 radix-state-on:text-[#0f0f10] radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 rounded-xl center p-0 h-10 w-10 text-gray-500 hover:text-gray-950 duration-100 transition-colors"
                x-data="true" @click="$dispatch('sb02-toggle')">
                <svg width="20px" height="20px" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"
                    color="currentColor" class="shrink-0 w-5 h-5">
                    <rect x="10.5" y="6.5" width="7" height="5" rx="1" transform="rotate(90 10.5 6.5)"
                        fill="currentColor"></rect>
                    <rect x="3" y="4" width="14" height="12" rx="2.8" stroke="currentColor"
                        stroke-width="1.5"></rect>
                </svg>
            </button>

            <!-- Page title -->
            <div class="hstack gap-2 items-center">
                <p data-testid="page-title" class="text-sm text-[#0f0f10] whitespace-nowrap font-medium">Files
                </p>
            </div>
        </div>
        <div class="flex-1"></div>

        <!-- Right action -->
        <div class="shrink-0 flex flex-row items-center gap-1">
            <!-- Notifications -->
            <button aria-label="Notifications" type="button" aria-expanded="false" data-state="closed"
                class="relative inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-colors duration-75 focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-transparent text-[#0f0f10] hover:bg-gray-100 radix-state-open:text-[#0f0f10] radix-state-open:bg-gray-200 radix-state-on:text-[#0f0f10] radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 rounded-[10px] center p-0 h-9 w-9">
                <div class="shrink-0 w-4.5 h-4.5 relative">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor">
                        <path
                            d="M16.875 13.5367C16.875 13.9997 16.4997 14.375 16.0368 14.375H3.96327C3.50031 14.375 3.125 13.9997 3.125 13.5367C3.125 13.4031 3.15696 13.2713 3.21822 13.1526L4.1665 11.3134C4.32966 10.997 4.42335 10.6494 4.44131 10.2938L4.58626 7.42413C4.7305 4.54901 7.11155 2.29166 10 2.29166C12.8884 2.29166 15.2695 4.54901 15.4137 7.42413L15.5587 10.2938C15.5767 10.6494 15.6703 10.997 15.8335 11.3134L16.7817 13.1526C16.843 13.2713 16.875 13.4031 16.875 13.5367Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                            d="M13.3332 14.375C13.3332 16.2159 11.8408 17.7083 9.99984 17.7083C8.15889 17.7083 6.6665 16.2159 6.6665 14.375"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg>
                </div>
            </button>

            <!-- Your profile action -->
            <button aria-label="Your profile"
                class="relative shrink-0 rounded-full focus-ring transition-all duration-150" type="button">
                <div class="relative size-9.25">
                    <div class="flex relative border-[#00001d13]" style="opacity: 1;">
                        <div class="opacity-100 transition-opacity duration-300 transform-[rotate(18deg)]  inline-block absolute items-center text-[2.3rem]"
                            aria-valuemax="100" aria-valuemin="0" aria-valuenow="1" role="progressbar">
                            <svg viewBox="0 0 100 100" class="size-[2.3rem]">
                                <circle cx="50" cy="50" r="42" stroke-width="6px"
                                    class="fill-transparent stroke-transparent"></circle>
                                <circle cx="50" cy="50" r="42" stroke-width="6px"
                                    class="stroke-black fill-transparent" stroke-linecap="round" stroke-dashoffset="66"
                                    stroke-dasharray="2.64 261.36"></circle>
                            </svg>
                        </div>
                        <div class="absolute! left-0 top-0 opacity-100 transition-opacity duration-300 transform-[rotateY(180deg)] inline-block items-center text-[2.3rem]"
                            aria-valuemax="100" aria-valuemin="0" aria-valuenow="88" role="progressbar">
                            <svg viewBox="0 0 100 100" class="size-[2.3rem]">
                                <circle cx="50" cy="50" r="42" stroke-width="6px"
                                    class="stroke-transparent fill-transparent"></circle>
                                <circle cx="50" cy="50" r="42" stroke-width="6px"
                                    class="stroke-[#d1d5db] fill-transparent" stroke-linecap="round"
                                    stroke-dashoffset="66" stroke-dasharray="232.32000000000002 31.67999999999998">
                                </circle>
                            </svg>
                        </div>
                    </div>
                    <div class="absolute top-0 left-0 h-full w-full rounded-full p-px"
                        style="opacity: 1; transform: scale(0.65);">
                        <img alt="Lewis Meta" loading="lazy" width="40" height="40" decoding="async"
                            data-nimg="1" class="rounded-full shrink-0 bg-gray-50 object-cover h-full w-full"
                            src="{{ asset('profile/profile.webp') }}"
                            style="color: transparent;">
                    </div>
                </div>
            </button>
        </div>
    </div>
</header>

<!-- Header Large Screen -->
<header class="hidden lg:block fixed top-0 right-0 transition-[width] duration-150 group/header-fixed z-30 h-[3.2rem]"
    :class="{ 'w-[calc(100%-16rem)]': show, 'w-[calc(100%-3.125rem)]': !show }" x-data="{ show: true }">
    <div class="h-12.5 w-full mx-auto flex flex-row items-center gap-2 bg-[#ffffffe6] backdrop-blur-sm border-b border-b-[#00001d13] px-2.5"
        style="opacity: 1; visibility: visible;">
        <!-- Toggle button -->
        <flux:tooltip :content="__('Close sidebar')" position="right">
            <button data-state="closed" @click="$dispatch('colapse-toggle')" x-on:click="show = false"
                x-data="true"
                class="relative inline-flex items-center justify-center whitespace-nowrap text-sm font-medium focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-transparent hover:bg-gray-100 radix-state-open:text-[#0f0f10] radix-state-open:bg-gray-200 radix-state-on:text-[#0f0f10] radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 rounded-[10px] pointer-events-auto p-0 h-8 w-8 text-gray-500 hover:text-gray-950 duration-100 transition-colors shrink-0 cursor-w-resize"
                x-show="show">
                <svg width="20px" height="20px" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" color="currentColor" class="w-5 h-5">
                    <rect x="7" y="6.5" width="7" height="1.5" rx="0.75" transform="rotate(90 7 6.5)"
                        fill="currentColor"></rect>
                    <rect x="3" y="4" width="14" height="12" rx="2.8" stroke="currentColor"
                        stroke-width="1.5"></rect>
                </svg>
            </button>
        </flux:tooltip>

        <flux:tooltip :content="__('Open sidebar')" position="right">
            <button data-state="opened" @click="$dispatch('open-toggle')" x-on:click="show = true"
                class="relative inline-flex items-center justify-center whitespace-nowrap text-sm font-medium focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-transparent hover:bg-gray-100 radix-state-open:text-[#0f0f10] radix-state-open:bg-gray-200 radix-state-on:text-[#0f0f10] radix-state-on:bg-gray-200 active:bg-gray-200 disabled:bg-transparent disabled:text-gray-400 rounded-[10px] pointer-events-auto p-0 h-8 w-8 text-gray-500 hover:text-gray-950 duration-100 transition-colors shrink-0 cursor-w-resize"
                x-show="!show">
                <svg width="20px" height="20px" viewBox="0 0 20 20" fill="none"
                    xmlns="http://www.w3.org/2000/svg" color="currentColor" class="w-5 h-5">
                    <rect x="7" y="6.5" width="7" height="1.5" rx="0.75" transform="rotate(90 7 6.5)"
                        fill="currentColor"></rect>
                    <rect x="3" y="4" width="14" height="12" rx="2.8" stroke="currentColor"
                        stroke-width="1.5"></rect>
                </svg>
            </button>
        </flux:tooltip>

        <!-- Page title -->
        <div class="hstack gap-1.5 items-center whitespace-nowrap min-w-0 overflow-hidden w-full py-1 px-1 -mr-1">
            <div class="shrink-0">
                <a data-agent-id="link-_r_1o0_" class="flex focus-ring outline-none rounded-md" href="#">
                    <p data-testid="page-title"
                        class="text-sm text-[#0f0f10] font-medium truncate hover:text-gray-950">
                        Files
                    </p>
                </a>
            </div>
            <div class="flex flex-row gap-1.5 items-center shrink-0"
                style="position: absolute; visibility: hidden; pointer-events: none;">
                <svg width="20px" height="20px" viewBox="0 0 16 16" fill="none"
                    xmlns="http://www.w3.org/2000/svg" class="text-gray-400 shrink-0">
                    <path
                        d="M6.66675 10.6667L8.86201 8.4714C9.12235 8.21107 9.12235 7.78893 8.86201 7.5286L6.66675 5.33333"
                        stroke="currentColor" stroke-width="1.33333" stroke-linecap="round" stroke-linejoin="round">
                    </path>
                </svg>
                <p class="text-sm font-medium text-gray-600 shrink-0">…</p>
            </div>
        </div>

        <div class="flex-1"></div>

        <div class="flex flex-row items-center max-h-full w-fit gap-2 empty:hidden">

            <div class="contents">
                <div class="hidden lg:contents">
                    <button type="button" aria-haspopup="dialog" aria-expanded="false" aria-controls="radix-_r_5v_"
                        data-state="closed" data-agent-id="button-_r_61_"
                        class="relative inline-flex text-xs items-center justify-center whitespace-nowrap font-medium transition-colors duration-75 focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-white border border-gray-200 hover:bg-gray-50 active:bg-gray-100 hover:border-gray-300 text-[#0f0f10] shadow-none active:border-gray-300 disabled:bg-white disabled:text-gray-300 disabled:border-gray-200 radix-state-open:bg-gray-50 radix-state-open:border-gray-300 radix-state-on:bg-gray-50 radix-state-on:border-gray-300 h-8 px-2.5 text-xm rounded-[0.6rem]">
                        Feedback
                    </button>
                </div>
            </div>

            <!-- Notifications -->
            <button aria-label="Notifications" type="button" aria-haspopup="dialog" aria-expanded="false"
                aria-controls="radix-_r_218_" data-state="closed" data-agent-id="button-_r_21a_"
                class="relative inline-flex items-center justify-center whitespace-nowrap font-medium transition-colors duration-75 focus-ring disabled:pointer-events-auto data-[loading='true']:text-transparent! bg-white border border-gray-200 hover:bg-gray-50 active:bg-gray-100 hover:border-gray-300 text-[#0f0f10] shadow-none active:border-gray-300 disabled:bg-white disabled:text-gray-300 disabled:border-gray-200 radix-state-open:bg-gray-50 radix-state-open:border-gray-300 radix-state-on:bg-gray-50 radix-state-on:border-gray-300 text-xm center p-0 h-8 w-8 shrink-0 rounded-[0.6rem] [&amp;&gt;div]:w-4 [&amp;&gt;div]:h-4">
                <div class="shrink-0 w-4 h-4 relative">
                    <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor">
                        <path
                            d="M16.875 13.5367C16.875 13.9997 16.4997 14.375 16.0368 14.375H3.96327C3.50031 14.375 3.125 13.9997 3.125 13.5367C3.125 13.4031 3.15696 13.2713 3.21822 13.1526L4.1665 11.3134C4.32966 10.997 4.42335 10.6494 4.44131 10.2938L4.58626 7.42413C4.7305 4.54901 7.11155 2.29166 10 2.29166C12.8884 2.29166 15.2695 4.54901 15.4137 7.42413L15.5587 10.2938C15.5767 10.6494 15.6703 10.997 15.8335 11.3134L16.7817 13.1526C16.843 13.2713 16.875 13.4031 16.875 13.5367Z"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path
                            d="M13.3332 14.375C13.3332 16.2159 11.8408 17.7083 9.99984 17.7083C8.15889 17.7083 6.6665 16.2159 6.6665 14.375"
                            stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                    </svg>
                </div>
            </button>

            <div x-data="{
                dropdownOpen: false
            }" class="relative">
                <button aria-label="Your profile" x-on:click="dropdownOpen=true"
                    class="relative shrink-0 rounded-full focus-ring transition-all duration-150" type="button">
                    <div class="relative size-9.25">
                        <div class="flex relative border-[#00001d13]" style="opacity: 1;">
                            <div class="opacity-100 transition-opacity duration-300 transform-[rotate(18deg)]  inline-block absolute items-center text-[2.3rem]"
                                aria-valuemax="100" aria-valuemin="0" aria-valuenow="1" role="progressbar">
                                <svg viewBox="0 0 100 100" class="size-[2.3rem]">
                                    <circle cx="50" cy="50" r="42" stroke-width="6px"
                                        class="fill-transparent stroke-transparent"></circle>
                                    <circle cx="50" cy="50" r="42" stroke-width="6px"
                                        class="stroke-black fill-transparent" stroke-linecap="round"
                                        stroke-dashoffset="66" stroke-dasharray="2.64 261.36"></circle>
                                </svg>
                            </div>
                            <div class="absolute! left-0 top-0 opacity-100 transition-opacity duration-300 transform-[rotateY(180deg)] inline-block items-center text-[2.3rem]"
                                aria-valuemax="100" aria-valuemin="0" aria-valuenow="88" role="progressbar">
                                <svg viewBox="0 0 100 100" class="size-[2.3rem]">
                                    <circle cx="50" cy="50" r="42" stroke-width="6px"
                                        class="stroke-transparent fill-transparent"></circle>
                                    <circle cx="50" cy="50" r="42" stroke-width="6px"
                                        class="stroke-[#d1d5db] fill-transparent" stroke-linecap="round"
                                        stroke-dashoffset="66"
                                        stroke-dasharray="232.32000000000002 31.67999999999998">
                                    </circle>
                                </svg>
                            </div>
                        </div>
                        <div class="absolute top-0 left-0 h-full w-full flex items-center justify-center rounded-full p-px"
                            style="opacity: 1; transform: scale(0.65);">
                            <img alt="Lewis Meta" loading="lazy" width="40" height="40" decoding="async"
                                data-nimg="1"
                                class="rounded-full flex items-center justify-center shrink-0 bg-gray-50 object-cover h-full w-full"
                                src="{{ asset('profile/profile.webp') }}" style="color: transparent;">
                        </div>
                    </div>
                </button>

                <div x-show="dropdownOpen" x-on:click.away="dropdownOpen=false"
                    x-transition:enter="ease-out duration-200" x-transition:enter-start="-translate-y-2"
                    x-transition:enter-end="translate-y-0" class="absolute top-0 right-0 z-50 mt-12 w-56" x-cloak>
                    <div class="p-1 mt-1 bg-white rounded-md border shadow-md border-neutral-200/70 text-neutral-700">
                        <div class="px-2 py-1.5 text-sm font-semibold">My Account</div>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <a href="#_"
                            class="relative flex cursor-default select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="mr-2 w-4 h-4">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <span>Profile</span>
                            <span class="ml-auto text-xs tracking-widest opacity-60">⇧⌘P</span>
                        </a>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <div class="relative group">
                            <div
                                class="relative flex cursor-default select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="mr-2 w-4 h-4">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <line x1="19" x2="19" y1="8" y2="14"></line>
                                    <line x1="22" x2="16" y1="11" y2="11"></line>
                                </svg>
                                <span>Invite users</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" class="ml-auto w-4 h-4">
                                    <polyline points="9 18 15 12 9 6"></polyline>
                                </svg>
                            </div>
                            <div data-submenu
                                class="absolute top-0 left-0 invisible mr-1 opacity-0 duration-200 ease-out -translate-x-full group-hover:mr-0 group-hover:visible group-hover:opacity-100">
                                <div
                                    class="z-50 min-w-32 overflow-hidden rounded-md border bg-white p-1 shadow-md animate-in slide-in-from-left-1 w-40">
                                    <div x-on:click="dropdownOpen=false"
                                        class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-disabled:pointer-events-none data-disabled:opacity-50">
                                        <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                                        </svg>
                                        <span>Email</span>
                                    </div>
                                    <div x-on:click="dropdownOpen=false"
                                        class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                                        <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z">
                                            </path>
                                        </svg>
                                        <span>Message</span>
                                    </div>
                                    <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                                    <div x-on:click="dropdownOpen=false"
                                        class="relative flex cursor-default select-none items-center rounded px-2 py-1.5 hover:bg-neutral-100 text-sm outline-none data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                                        <svg class="mr-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <circle cx="12" cy="12" r="10"></circle>
                                            <line x1="12" x2="12" y1="8" y2="16">
                                            </line>
                                            <line x1="8" x2="16" y1="12" y2="12">
                                            </line>
                                        </svg>
                                        <span>More...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="-mx-1 my-1 h-px bg-neutral-200"></div>
                        <a href="#_"
                            class="relative flex cursor-default select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors focus:bg-accent focus:text-accent-foreground data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="mr-2 w-4 h-4">
                                <path
                                    d="M15 22v-4a4.8 4.8 0 0 0-1-3.5c3 0 6-2 6-5.5.08-1.25-.27-2.48-1-3.5.28-1.15.28-2.35 0-3.5 0 0-1 0-3 1.5-2.64-.5-5.36-.5-8 0C6 2 5 2 5 2c-.3 1.15-.3 2.35 0 3.5A5.403 5.403 0 0 0 4 9c0 3.5 3 5.5 6 5.5-.39.49-.68 1.05-.85 1.65-.17.6-.22 1.23-.15 1.85v4">
                                </path>
                                <path d="M9 18c-4.51 2-5-2-7-2"></path>
                            </svg>
                            <span>GitHub</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</header>
