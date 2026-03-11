<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-slate-900 mb-2">Confirm Password</h2>
        <p class="text-slate-500">Please confirm your password to continue</p>
    </div>

    <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-lg">
        <div class="flex items-start">
            <i class="ri-shield-keyhole-line text-orange-500 mt-0.5 mr-3 text-lg"></i>
            <p class="text-sm text-orange-700">
                {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
            </p>
        </div>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-6">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Current Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Enter your current password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-sm" />
        </div>

        <div class="space-y-4">
            <x-primary-button>
                <i class="ri-check-line mr-2"></i>
                {{ __('Confirm Password') }}
            </x-primary-button>
            
            <div class="text-center">
                <a href="{{ url()->previous() }}" class="text-sm font-bold text-primary-600 hover:text-primary-800 transition-colors flex items-center justify-center gap-1">
                    <i class="ri-arrow-left-line"></i>
                    Go Back
                </a>
            </div>
        </div>
    </form>
</x-guest-layout>
