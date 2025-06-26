<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\EmailConfiguration;
use App\Models\GeneralSetting;
use App\Models\LogoSetting;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $generalSettings = GeneralSetting::first();
        $emailSettings = EmailConfiguration::first();
        $logoSetting = LogoSetting::first();

        return view('admin.setting.index', compact(
            'generalSettings', 
            'emailSettings', 
            'logoSetting', 
        ));
    }


    public function updateGeneralSetting(Request $request)
    {
        $request->validate([
            'site_name' => ['required', 'max:200'],
            'currency_name' => ['required', 'max:200'],
            'currency_icon' => ['required', 'max:200'],
            'contact_email' => ['nullable', 'email', 'max:200'],
            'contact_phone' => ['nullable', 'max:200'],
            'contact_address' => ['nullable', 'max:500'],
            'api_price_markup_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'enable_dynamic_pricing' => ['nullable', 'boolean'],
            'naira_to_dollar_rate' => ['nullable', 'numeric', 'min:0'],
        ]);

        GeneralSetting::updateOrCreate(
            ['id' => 1],
            [
                'site_name' => $request->site_name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'contact_address' => $request->contact_address,
                'currency_name' => $request->currency_name,
                'currency_icon' => $request->currency_icon,
                'api_price_markup_percentage' => $request->api_price_markup_percentage ?? 20.00,
                'enable_dynamic_pricing' => $request->boolean('enable_dynamic_pricing', true),
                'naira_to_dollar_rate' => $request->naira_to_dollar_rate,
            ]
        );

        toastr('General settings successfully updated', 'success', ['success']);

        return redirect()->back();

    }

    public function updateEmailSetting(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'host' => ['required', 'max:200'],
            'username' => ['required', 'max:200'],
            'password' => ['required', 'max:200'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'encryption' => ['required', 'in:tls,ssl,none'],
        ]);

         EmailConfiguration::updateOrCreate(
            ['id' => 1],
            [
                'email' => $request->email,
                'host' => $request->host,
                'username' => $request->username,
                'password' => $request->password,
                'port' => $request->port,
                'encryption' => $request->encryption,
            ]
        );

        toastr('Email settings successfully updated', 'success', ['success']);
        return redirect()->back();
    }

    public function updateLogoSetting(Request $request)
    {
        $request->validate([
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg', 'max:3000'],
            'favicon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,ico', 'max:1000'],
        ]);

        $logoPath = $this->updateImage($request, 'logo', 'uploads', $request->old_logo);
        $favicon = $this->updateImage($request, 'favicon', 'uploads', $request->old_favicon);

       LogoSetting::updateOrCreate(
            ['id' => 1],
            [
                'logo' =>  (!empty($logoPath)) ? $logoPath : $request->old_logo,
                'favicon' => (!empty($favicon)) ? $favicon : $request->old_favicon
            ]
        );

        toastr('Logo settings successfully updated', 'success', ['success']);

        return redirect()->back();
    }
}
