<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the admin's profile form.
     */
    public function index()
    {
        return view('admin.profile.index');
    }

    /**
     * Update the admin's profile information.
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        
        // If email is being changed, automatically verify it for admin users
        if ($user->email !== $request->email) {
        $user->email = $request->email;
            $user->email_verified_at = now();
        }
        
        $user->save();


        toastr()->success('Profile updated successfully!');

        return redirect()->route('admin.profile.index');
    }

    /**
     * Update the admin's password.
     */
    public function updatePassword(Request $request)
    {
         $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required','confirmed', 'min:8']
        ]);

        $request->user()->update([
            'password' => bcrypt($request->password)
        ]);

        toastr()->success('Password updated successfully!');       
        return redirect()->route('admin.profile.index');
    }
}