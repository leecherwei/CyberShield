<x-guest-layout>
    <!-- Header / Branding -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-900">{{ __('Create an Account') }}</h2>
        <p class="mt-1 text-sm text-gray-600">{{ __('Set up your personal and organization profile to get started.') }}</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Full Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" />
            <x-text-input 
                id="name" 
                class="block mt-1 w-full" 
                type="text" 
                name="name" 
                :value="old('name')" 
                placeholder="John Doe"
                required 
                autofocus 
                autocomplete="name" 
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input 
                id="email" 
                class="block mt-1 w-full" 
                type="email" 
                name="email" 
                :value="old('email')" 
                placeholder="name@company.com"
                required 
                autocomplete="username" 
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Organisation Name -->
        <div class="mt-4">
            <x-input-label for="organisation_name" :value="__('Organisation Name')" />
            <x-text-input 
                id="organisation_name" 
                class="block mt-1 w-full" 
                type="text" 
                name="organisation_name"
                :value="old('organisation_name')" 
                placeholder="Acme Corp Ltd."
                required 
            />
            <x-input-error :messages="$errors->get('organisation_name')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input 
                id="password" 
                class="block mt-1 w-full" 
                type="password" 
                name="password" 
                placeholder="••••••••"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input 
                id="password_confirmation" 
                class="block mt-1 w-full" 
                type="password"
                name="password_confirmation" 
                placeholder="••••••••"
                required 
                autocomplete="new-password" 
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-2.5 text-base">
                {{ __('Create Account') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Navigation to Login -->
    <div class="mt-6 text-center text-sm text-gray-600">
        {{ __('Already have an account?') }}
        <a class="font-medium text-indigo-600 hover:text-indigo-500 focus:outline-none focus:underline"
            href="{{ route('login') }}">
            {{ __('Sign in') }}
        </a>
    </div>
</x-guest-layout>