<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Country;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page with services and countries data
     */
    public function index()
    {
        $services = Service::where('status', 'active')
                          ->orderBy('name')
                          ->take(6)
                          ->get();
        
        $countries = Country::orderBy('name')
                           ->take(8)
                           ->get();
        
        $totalServices = Service::where('status', 'active')->count();
        $totalCountries = Country::count();
        
        return view('home', compact('services', 'countries', 'totalServices', 'totalCountries'));
    }
}