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
    public function usaNumbers()
    {
        $services = Service::all();
        $countries = Country::all();

        // Filter orders for USA numbers only (assuming country code 7 is USA)
        $orders = Order::where('user_id', Auth::user()->id)
            ->whereHas('service', function($query) {
                // You might need to adjust this based on how you store country info
                // For now, we'll filter by phone number prefix or add country field to orders
            })
            ->latest()
            ->paginate(10);

        return view('user.usa-numbers', compact(
            'services',
            'orders',
            'countries'
        ));
    }

    public function allCountriesNumbers()
    {
        $services = Service::all();
        $countries = Country::all();

        $orders = Order::where('user_id', Auth::user()->id)
            ->latest()
            ->paginate(10);

        return view('user.all-countries-numbers', compact(
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
