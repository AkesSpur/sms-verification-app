<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Paystack;
use Illuminate\Http\Request;

class PaystackSettingController extends Controller
{
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => ['required', 'integer'],
            'country_name' => ['required', 'max:200'],
            'currency_name' => ['required', 'max:200'],
            'public_key' => ['required'],
            'secret_key' => ['required']
        ]);
        Paystack::updateOrCreate(
            ['id' => $id],
            [
                'status' => $request->status,
                'country_name' => $request->country_name,
                'currency_name' => $request->currency_name,
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
            ]
        );

        toastr('Updated Successfully!', 'success');
        return redirect()->back();

    }
}
