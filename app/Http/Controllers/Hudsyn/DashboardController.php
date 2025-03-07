<?php

namespace App\Http\Controllers\Hudsyn;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Optionally, gather statistics or data to display on your dashboard.
        return view('hudsyn.dashboard.index');
    }
}
