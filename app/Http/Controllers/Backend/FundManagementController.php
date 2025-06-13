<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'amount'=> ['required','numeric','min:1','max:10000']
        ]);
        
        $user = User::findOrFail($id);
        $oldBalance = $user->balance;
        $newBalance = $oldBalance + $request->amount;

        $user->update([
            'balance' => $newBalance
        ]);

        // Log the transaction (you can create a Transaction model later if needed)
        Log::info('Admin added funds', [
            'user_id' => $id,
            'amount' => $request->amount,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'admin_id' => auth()->id()
        ]);

        // return response(['status' => 'success', 'message' => 'Balance successfully added']);
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
            'amount'=> ['required','numeric','min:1','max:10000']
        ]);
        
        $user = User::findOrFail($id);
        $oldBalance = $user->balance;

        if ($request->amount > $oldBalance) {
            return response(['status' => 'error', 'message' => 'User has insufficient balance']);
        }

        $newBalance = $oldBalance - $request->amount;

        $user->update([
            'balance' => $newBalance
        ]);

        // Log the transaction (you can create a Transaction model later if needed)
        Log::info('Admin withdrew funds', [
            'user_id' => $id,
            'amount' => $request->amount,
            'old_balance' => $oldBalance,
            'new_balance' => $newBalance,
            'admin_id' => auth()->id()
        ]);

        // return response(['status' => 'success', 'message' => 'Funds successfully withdrawn']);
        toastr()->success('Funds successfully withdrawn');
        return redirect()->back();
    }
}
