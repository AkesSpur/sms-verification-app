@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="space-y-5 max-w-4xl mx-auto">

    {{-- Flash messages --}}
    @if(session('status') === 'profile-updated')
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-xs">
        <i class="ri-check-line text-emerald-500 flex-shrink-0"></i>
        Profile updated successfully!
    </div>
    @endif

    @if(session('status') === 'password-updated')
    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-2xl text-xs">
        <i class="ri-check-line text-emerald-500 flex-shrink-0"></i>
        Password updated successfully!
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ── Profile Information ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Profile Information</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-xs font-medium text-gray-500 mb-1.5">Name</label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $user->name) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-medium text-gray-500 mb-1.5">Phone Number</label>
                    <input type="text" id="phone" name="phone" maxlength="11"
                           value="{{ old('phone', $user->phone) }}"
                           placeholder="08012345678" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    <p class="text-[11px] text-gray-400 mt-1">Required to access your dashboard.</p>
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-xs font-medium text-gray-500 mb-1.5">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $user->email) }}" required
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-2 text-xs text-gray-500">
                        Your email is unverified.
                        <button form="send-verification" class="underline text-primary-600 hover:text-primary-800">
                            Re-send verification email
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <p class="mt-1 text-emerald-600">Verification link sent to your email.</p>
                        @endif
                    </div>
                    @endif
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit"
                            class="flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                            style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                        <i class="ri-save-line"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Update Password ── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-gray-400 mb-4">Update Password</p>
            <p class="text-xs text-gray-400 mb-4 -mt-2">Use a long, random password to keep your account secure.</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('put')

                <div>
                    <label for="update_password_current_password" class="block text-xs font-medium text-gray-500 mb-1.5">Current Password</label>
                    <input type="password" id="update_password_current_password"
                           name="current_password" autocomplete="current-password"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    @error('current_password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password" class="block text-xs font-medium text-gray-500 mb-1.5">New Password</label>
                    <input type="password" id="update_password_password"
                           name="password" autocomplete="new-password"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    @error('password', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="update_password_password_confirmation" class="block text-xs font-medium text-gray-500 mb-1.5">Confirm Password</label>
                    <input type="password" id="update_password_password_confirmation"
                           name="password_confirmation" autocomplete="new-password"
                           class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl focus:ring-2 focus:ring-primary-200 focus:border-primary-400 outline-none transition-all">
                    @error('password_confirmation', 'updatePassword')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end pt-1">
                    <button type="submit"
                            class="flex items-center gap-1.5 px-5 py-2 rounded-xl text-xs font-bold text-white transition-all btn-glow"
                            style="background: linear-gradient(135deg, #475569 0%, #1e293b 100%);">
                        <i class="ri-lock-password-line"></i> Update Password
                    </button>
                </div>
            </form>
        </div>

    </div>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif

</div>
@endsection
