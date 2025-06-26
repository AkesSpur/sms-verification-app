<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Mail\AccountCreatedMail;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ManageUserController extends Controller
{
    //manage users Index page
    public function index(){

        return view('admin.manage-user.index');

    }


    public function create(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'email' => ['required', 'email', 'unique:users,email', 'max:255'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => ['required', 'in:client,admin'],
            'balance' => ['nullable', 'numeric', 'min:0', 'max:100000']
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => 'active',
            'balance' => $request->balance ?? 0.00,
            'is_admin' => $request->role === 'admin',
            'email_verified_at' => now(), // Auto-verify admin created accounts
        ]);

        try {
            Mail::to($request->email)->send(new AccountCreatedMail($request->name, $request->email, $request->password));
        } catch (Exception $e) {
            Log::warning('Failed to send account creation email', [
                'user_id' => $user->id,
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);
        }

        toastr()->success('User successfully created');

        return redirect()->route('admin.manage-user.index');
    }
}
