<?php

namespace App\Http\Controllers;

use App\Models\BlacklistedNumber;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // App/Http/Controllers/AdminController.php
public function orders()
{
    $orders = Order::with(['user', 'service'])->paginate(10);
    return view('admin.orders', compact('orders'));
}

public function blacklist()
{
    $blacklistedNumbers = BlacklistedNumber::with('service')->paginate(10);
    return view('admin.blacklist', compact('blacklistedNumbers'));
}

public function services()
{
    $services = Service::paginate(10);
    return view('admin.services', compact('services'));
}
}
