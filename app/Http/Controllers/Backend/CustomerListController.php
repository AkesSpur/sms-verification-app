<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class CustomerListController extends Controller
{
    // return customer list view page
    public function index(){

        $customers = User::where('role','=','client')->with('orders')->get();
        
        return view('admin.customer-list.index',compact('customers'));
        
    }

    public function changeStatus(Request $request){
        
        $customer = User::findOrFail($request->id);
        $customer->status = $request->status == 'true' ? 'active' : 'inactive';
        $customer->save();

        return response(['message' => 'Status has been updated!']);
    }

    public function updateEmail(Request $request, string $id)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email,'.$id
        ]);

        $customer = User::findOrFail($id);
        $customer->email = $request->email;
        $customer->email_verified_at = now();
        $customer->save();

        return response(['status' => 'success', 'message' => 'Email successfully updated']);
    }

    public function verifyEmail(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = now();
        $user->save();

        return response(['status' => 'success', 'message' => 'The user email has been successfully verified']);
    }

    public function sendResetLink(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        
        $status = Password::sendResetLink(
            ['email' => $user->email]
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response(['status' => 'success', 'message' => 'Password reset link sent']);
        }

        return response(['status' => 'error', 'message' => 'Failed to send password reset link']);
    }

    public function destroy(string $id)
    {
        $customer = User::findOrFail($id);
        
        // Check if customer has any orders
        if ($customer->orders()->count() > 0) {
            return response(['status' => 'error', 'message' => 'Unable to delete a user with existing orders']);
        }
        
        $customer->delete();

        return response(['status' => 'success', 'message' => 'User successfully deleted']);
    }

}
