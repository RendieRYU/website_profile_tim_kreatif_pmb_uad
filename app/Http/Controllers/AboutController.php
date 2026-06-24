<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Division;

class AboutController extends Controller
{
    public function index()
    {
        $periods = \App\Models\Period::with(['members.division'])->orderByDesc('name')->get();
        return view('pages.about', compact('periods'));
    }
}
