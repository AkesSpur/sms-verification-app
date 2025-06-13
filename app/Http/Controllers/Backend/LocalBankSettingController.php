<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Localbank;
use Illuminate\Http\Request;

class LocalBankSettingController extends Controller
{
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => ['required', 'integer'],
            'bank_name' => ['required', 'max:200'],
            'account_name' => ['required','max:200'],
            'account_number' => ['required'],
            'extra_info' => ['required']
        ]);
        // dd($request->all());
        Localbank::updateOrCreate(
            ['id' => $id],
            [
                'status' => $request->status,
                'account_name' => $request->account_name,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'extra_info' => $request->extra_info,
            ]
        );

        toastr('Updated Successfully!', 'success');
        return redirect()->back();

    }

}
