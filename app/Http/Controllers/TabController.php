<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TabController extends Controller
{
    public function loadTab($tab)
    {
        // Validate the tab name
        if (!in_array($tab, ['dashboard', 'numbers', 'transactions', 'settings'])) {
            abort(404); // Return a 404 error if the tab is invalid
        }
        return view("components.{$tab}");
    }
}