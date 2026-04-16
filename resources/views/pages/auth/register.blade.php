<x-layouts::auth :title="__('Register')">
    <div class="w-full p-8 mx-auto shadow bg-linear-180 outline outline-zinc-800 from-zinc-800 to-zinc-950 rounded-xl">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex mt-10 flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input name="name" :label="__('Name')" :value="old('name')" type="text" required autofocus
                autocomplete="name" :placeholder="__('Full name')" />

            <!-- Email Address -->
            <flux:input name="email" :label="__('Email address')" :value="old('email')" type="email" required
                autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <flux:input name="password" :label="__('Password')" type="password" required autocomplete="new-password"
                :placeholder="__('Password')" viewable />

            <!-- Confirm Password -->
            <flux:input name="password_confirmation" :label="__('Confirm password')" type="password" required
                autocomplete="new-password" :placeholder="__('Confirm password')" viewable />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>

            <!-- OAuth providers -->
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-white/6"></div>
                </div>
                <div class="relative flex justify-center">
                    <span class="px-3 bg-[#111110] text-xs text-white/30">{{ __('or continue with') }}</span>
                </div>
            </div>
            <div class="grid grid-cols-1 gap-3">
                <a href="#"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-white/6 rounded-lg text-sm text-white/60 hover:text-white hover:border-white/20 transition-all">
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

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm mt-4 text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('sign in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
