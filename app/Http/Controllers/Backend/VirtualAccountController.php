<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\VirtualAccount;
use Illuminate\Http\Request;

class VirtualAccountController extends Controller
{
    /**
     * Display a listing of virtual accounts with their users.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $query = VirtualAccount::with('user')->orderByDesc('created_at');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('bank_code', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $accounts = $query->paginate(25)->appends($request->query());

        return view('admin.virtual-accounts.index', [
            'accounts' => $accounts,
            'search' => $search,
        ]);
    }
}