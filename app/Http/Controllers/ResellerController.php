<?php

namespace App\Http\Controllers;

use App\Models\ResellerProduct;
use App\Models\ResellerRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class ResellerController extends Controller
{
    /**
     * Show reseller store page.
     */
    public function index()
    {
        $user = Auth::user();
        $isReseller = $user && $user->isReseller();
        $products = [];
        if ($isReseller) {
            $products = ResellerProduct::active()->ordered()->get();
        }
        return view('user.reseller.index', compact('products', 'isReseller'));
    }

    /**
     * Submit reseller access request.
     */
    public function requestAccess()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        $user = Auth::user();

        $existing = ResellerRequest::where('user_id', $user->id)->where('status', 'pending')->first();
        if ($existing) {
            toastr('You already have a pending reseller request.', 'info');
            return redirect()->route('user.reseller');
        }

        ResellerRequest::create([
            'user_id' => $user->id,
            'status' => 'pending'
        ]);

        toastr('Reseller access request submitted successfully!', 'success');
        return redirect()->route('user.reseller');
    }
}