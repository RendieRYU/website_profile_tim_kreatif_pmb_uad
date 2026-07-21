<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Member;
use App\Models\Event;
use App\Models\News;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'anggota') {
            $stats = [
                'works' => \App\Models\Work::count(),
            ];
            return view('admin.dashboard_anggota', compact('stats'));
        }

        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        
        $periodYear = $activePeriod ? (int)$activePeriod->name : (int)date('Y');
        if ($periodYear < 2000) $periodYear = (int)date('Y');
        $startYear = $periodYear - 1;
        $startDate = $startYear . '-10-01';
        $endDate = $periodYear . '-09-30';

        $stats = [
            // Total All-Time
            'total_members' => Member::count(),
            'total_events' => Event::count(),
            'total_news' => News::count(),
            
            // Current Period Stats
            'period_members' => $activePeriod ? Member::where('period_id', $activePeriod->id)->count() : 0,
            'period_events' => $activePeriod ? Event::where('period_id', $activePeriod->id)->count() : 0,
            'period_news' => News::whereBetween('published_date', [$startDate, $endDate])->count(),
            'period_name' => $activePeriod ? $activePeriod->name : date('Y'),
        ];
        return view('admin.dashboard', compact('stats'));
    }
}
