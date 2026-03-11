<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">Verify Your Email</h2>
        <p class="text-slate-500">We've sent a verification link to your email</p>
    </div>

    <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
        <div class="flex items-start">
            <i class="ri-mail-line text-yellow-500 mt-0.5 mr-3 text-lg"></i>
            <p class="text-sm text-yellow-700">
                {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </p>
        </div>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
            <div class="flex items-start">
                <i class="ri-checkbox-circle-line text-green-500 mt-0.5 mr-3 text-lg"></i>
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
                <i class="ri-send-plane-fill mr-2"></i>
                {{ __('Resend Verification Email') }}
            </x-primary-button>
        </form>

        <div class="text-center">
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm font-bold text-slate-600 hover:text-slate-800 transition-colors flex items-center justify-center gap-1 mx-auto">
                    <i class="ri-logout-box-line"></i>
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-guest-layout>
