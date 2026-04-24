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
                <a href="{{ route('socialite.redirect', 'google') }}"
                    class="flex items-center justify-center gap-2 px-4 py-2 border border-white/6 rounded-lg text-sm text-white/60 hover:text-white hover:border-white/20 transition-all">
                    <img src="{{ asset('icons/google.svg') }}" class="size-4" alt="">
                    {{ __('Continue with Google') }}
                </a>
                 <a href="{{ route('socialite.redirect', 'facebook') }}"
                        class="flex items-center justify-center gap-2 px-4 py-2 border dark:border-white/6 border-gray-200 rounded-lg text-sm dark:text-white/60  dark:hover:text-white dark:hover:border-white/20 transition-all">
                        <img src="{{ asset('icons/facebook.svg') }}" class="size-5" alt="">
                        {{ __('Continue with Facebook') }}
                    </a>
                </div>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm mt-4 text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('sign in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
