<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsersController extends Controller
{
    public function dashboard()
    {
        $countries = Country::all();
        $services = Service::all();
        $balance = Auth::user()->balance;
        return view('user.dashboard', compact(
            'services',
            'balance',
            'countries'
        ));
    }
    public function number()
    {
        $services = Service::all();
        $countries = Country::all();

        $orders = Order::where(
            'user_id',
            Auth::user()->id
        )
            ->latest()
            ->paginate(5);

        return view('user.number', compact(
            'services',
            'orders',
            'countries'
        ));
    }
    public function transaction()
    {
        return view('user.transaction');
    }
}
