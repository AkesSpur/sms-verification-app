<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class AdminListController extends Controller
{
    //return adminlist view page
    public function index(){

        $admins = User::where('role','=','admin')->get();

        return view('admin.admin-list.index',compact('admins'));

    }

    public function changeStatus(Request $request){
        
        $customer = User::findOrFail($request->id);
        $customer->status = $request->status == 'true' ? 'active' : 'inactive';
        $customer->save();

        return response(['message' => 'Status has been updated!']);
    }

    // delete an admin
    public function destroy(string $id)
    {
        
        $admin = User::findOrFail($id);
        $admin->delete();

        return response(['status' => 'success', 'message' => 'Successfully deleted']);

    }

    public function updateEmail(Request $request, string $id)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $admin = User::findOrFail($id);
        $admin->email = $request->email;
        $admin->email_verified_at = now();
        $admin->save();

        return response(['status' => 'success', 'message' => 'Email successfully updated']);
    }

    public function verifyEmail(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        return response(['status' => 'success', 'message' => 'User email has been successfully verified']);
    }

    public function sendResetLink(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        if ($status == Password::RESET_LINK_SENT) {
            return response(['status' => 'success', 'message' => __($status)]);
        } else {
            return response(['status' => 'error', 'message' => __($status)], 422);
        }
    }
}
