<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold gradient-text mb-2">Verify Your Email</h2>
        <p class="text-gray-600">We've sent a verification link to your email</p>
    </div>

    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-envelope text-yellow-500 mt-0.5 mr-3"></i>
            <p class="text-sm text-yellow-700">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <i class="fas fa-check-circle text-green-500 mt-0.5 mr-3"></i>
                <p class="text-sm text-green-700">
                    {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                </p>
            </div>
        </div>
    @endif

    <div class="space-y-4">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button>
                <i class="fas fa-paper-plane mr-2"></i>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="auth-link text-sm font-semibold">
                    <i class="fas fa-sign-out-alt mr-1"></i>
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
