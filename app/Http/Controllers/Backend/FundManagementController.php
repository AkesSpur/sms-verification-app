<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class FundManagementController extends Controller
{
    // return view page
    public function addIndex(string $id)
    {
        $userId = $id;

        return view('admin.fund-management.add-fund',compact('userId'));
    }

    // run deposit function
    public function addFund(Request $request, string $id)
    {
        $request->validate([
            'amount'=> ['required','numeric','min:1','max:500000']
        ]);
        
        $user = User::findOrFail($id);
        $admin = Auth::user();
        
        // Add balance with transaction logging
        $user->addBalance(
            $request->amount,
            'fund_addition',
            "Admin {$admin->name} added funds to {$user->name}'s account",
            null,
            $admin
        );

        // Log the transaction for additional logging
        Log::info('Admin added funds', [
            'user_id' => $id,
            'amount' => $request->amount,
            'admin_id' => $admin->id,
            'new_balance' => $user->fresh()->balance
        ]);

        toastr()->success('Funds successfully added');
        return redirect()->back();
    }
    
    // return view page
    public function withdrawIndex(string $id)
    {
        $userId = $id;
        
        return view('admin.fund-management.withdraw-fund',compact('userId'));
    }
    
    // run withdraw function
    public function withdrawFund(Request $request, string $id)
    {
        $request->validate([
            'amount'=> ['required','numeric','min:1','max:500000']
        ]);
        
        $user = User::findOrFail($id);
        $admin = Auth::user();

        if ($request->amount > $user->balance) {
            toastr()->error('User has insufficient balance');
            return redirect()->back();
        }

        // Deduct balance with transaction logging
        $user->deductBalance(
            $request->amount,
            'fund_withdrawal',
            "Admin {$admin->name} withdrew funds from {$user->name}'s account",
            null,
            $admin
        );

        // Log the transaction for additional logging
        Log::info('Admin withdrew funds', [
            'user_id' => $id,
            'amount' => $request->amount,
            'admin_id' => $admin->id,
            'new_balance' => $user->fresh()->balance
        ]);

        toastr()->success('Funds successfully withdrawn');
        return redirect()->back();
    }
}
