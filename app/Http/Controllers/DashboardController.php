<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Setting;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');
        $members = Member::with(['period', 'division'])->get();
        $latestNews = \App\Models\News::orderByDesc('published_date')->take(3)->get();
        
        $divisions = \App\Models\Division::all();
        $divisionEventCounts = \Illuminate\Support\Facades\DB::table('event_member')
            ->select('division_id', \Illuminate\Support\Facades\DB::raw('count(distinct event_id) as total'))
            ->groupBy('division_id')
            ->pluck('total', 'division_id');

        return view('pages.dashboard', compact('settings', 'members', 'latestNews', 'divisions', 'divisionEventCounts'));
    }
}
