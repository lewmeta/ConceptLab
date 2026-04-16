<x-layouts::auth :title="__('Sign in')">
    <div class="flex">
        <div
            class="max-w-md w-full p-8 mx-auto shadow bg-linear-180 outline dark:outline-zinc-800 outline-zinc-100 from-zinc-50 to-zinc-100 dark:from-zinc-800 dark:to-zinc-950 rounded-xl ">
            <x-auth-header :title="__('Sign in')" :description="__('Enter your credentials to access your workspace.')" />

            <!-- Session Status -->
            <x-auth-session-status class="text-center" :status="session('status')" />

            <form method="POST" action="{{ route('login.store') }}" class="mt-10 space-y-4">
                @csrf

                <!-- Email Address -->
                <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                    autofocus autocomplete="email" placeholder="email@example.com" />

                <!-- Password -->
                <div class="relative">
                    <flux:input name="password" :label="__('Password')" type="password" required
                        autocomplete="current-password" :placeholder="__('Password')" viewable />

                    @if (Route::has('password.request'))
                        <flux:link class="absolute top-0 text-sm inset-e-0" :href="route('password.request')"
                            wire:navigate>
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    @endif
                </div>

                <!-- Remember Me -->
                <flux:checkbox name="remember" :label="__('Remember me for 30 days')" :checked="old('remember')" />

                <!-- Sign in -->
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                        {{ __('Sign in with email') }}
                    </flux:button>
                </div>

                <!-- OAuth providers -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-white/6"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <span class="px-3 dark:bg-[#111110] text-xs text-white/30">{{ __('or continue with') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('socialite.redirect', 'google') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2 border dark:border-white/6 border-gray-200 rounded-lg text-sm dark:text-white/60  dark:hover:text-white dark:hover:border-white/20 transition-all">
                        <img src="{{ asset('icons/google.svg') }}" class="size-4" alt="">
                        {{ __('Continue with Google') }}
                    </a>
                    {{-- <a href="#"
                        class="flex items-center justify-center gap-2 px-4 py-2 border border-white/6 rounded-lg text-sm text-white/60 hover:text-white hover:border-white/20 transition-all">
                        <svg viewBox="0 0 24 24" fill="#0064E0" class="size-4 text-white bg-[#FFFFFF] rounded-xs">
                            <path
                                d="M22 12.037C22 6.494 17.523 2 12 2S2 6.494 2 12.037c0 4.707 3.229 8.656 7.584 9.741v-6.674H7.522v-3.067h2.062v-1.322c0-3.416 1.54-5 4.882-5 .634 0 1.727.125 2.174.25v2.78a12.807 12.807 0 0 0-1.155-.037c-1.64 0-2.273.623-2.273 2.244v1.085h3.266l-.56 3.067h-2.706V22C18.164 21.4 22 17.168 22 12.037z">
                            </path>
                        </svg>
                        {{ __('Facebook') }}
                    </a> --}}
                </div>
            </form>

            @if (Route::has('register'))
                <div class="space-x-1 mt-4 text-sm text-center rtl:space-x-reverse text-zinc-600 dark:text-zinc-400">
                    <span>{{ __('Don\'t have an account?') }}</span>
                    <flux:link :href="route('register')" wire:navigate>{{ __('sign up') }}</flux:link>
                </div>
            @endif
        </div>
    </div>
</x-layouts::auth>
