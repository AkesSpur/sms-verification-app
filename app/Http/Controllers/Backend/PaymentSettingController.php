<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Localbank;
use App\Models\Paystack;
use Illuminate\Http\Request;

class PaymentSettingController extends Controller
{
    public function index()
    {
        $paystackSetting = Paystack::first();
        $localbankSetting = Localbank::first();
        
        return view('admin.payment-settings.index',compact('paystackSetting','localbankSetting'));
    }
}
