<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold gradient-text mb-2">Reset Password</h2>
        <p class="text-gray-600">Enter your email to receive a password reset link</p>
    </div>

    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
            <p class="text-sm text-blue-700">
                {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
            </p>
        </div>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email Address')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus placeholder="Enter your email address" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-sm" />
        </div>

        <div class="space-y-4">
            <x-primary-button>
                {{ __('Send Reset Link') }}
            </x-primary-button>
            
            <div class="text-center">
                <a href="{{ route('login') }}" class="auth-link text-sm font-semibold">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Sign In
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
