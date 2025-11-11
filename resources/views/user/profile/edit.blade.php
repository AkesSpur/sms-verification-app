@extends('layouts.user')

@section('title', 'Profile')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Profile Settings</h1>
        <p class="text-gray-600">Manage your account information and security settings.</p>
    </div>

    @if(session('status') === 'profile-updated')
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            Profile updated successfully!
        </div>
    @endif

    @if(session('status') === 'password-updated')
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg">
            Password updated successfully!
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Profile Information</h2>
                <p class="text-sm text-gray-600 mb-6">Update your account's profile information and email address.</p>
                
                <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')
                    
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               maxlength="11"
                               value="{{ old('phone', $user->phone) }}"
                               placeholder="Example: 08012345678"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Provide a valid phone number; it’s required to access your dashboard.</p>
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                        
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                            <div class="mt-2">
                                <p class="text-sm text-gray-800">
                                    Your email address is unverified.
                                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Click here to re-send the verification email.
                                    </button>
                                </p>
                                
                                @if (session('status') === 'verification-link-sent')
                                    <p class="mt-2 font-medium text-sm text-green-600">
                                        A new verification link has been sent to your email address.
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Update Password -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Update Password</h2>
                <p class="text-sm text-gray-600 mb-6">Ensure your account is using a long, random password to stay secure.</p>
                
                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')
                    
                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" 
                               id="update_password_current_password" 
                               name="current_password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" 
                               id="update_password_password" 
                               name="password"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               autocomplete="new-password">
                        @error('password', 'updatePassword')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" 
                               id="update_password_password_confirmation" 
                               name="password_confirmation"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                               autocomplete="new-password">
                        @error('password_confirmation', 'updatePassword')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="flex justify-end">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Email Verification Form -->
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif
</div>
@endsection