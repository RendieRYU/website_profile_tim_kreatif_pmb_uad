<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        return view('pages.calendar', compact('activePeriod'));
    }
}
