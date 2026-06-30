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
        
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            $activePeriod = new \App\Models\Period(['name' => date('Y'), 'id' => 0]);
        }

        $divisions = \App\Models\Division::all();
        $divisionEventCounts = \Illuminate\Support\Facades\DB::table('event_member')
            ->join('events', 'event_member.event_id', '=', 'events.id')
            ->where('events.period_id', $activePeriod->id)
            ->select('division_id', \Illuminate\Support\Facades\DB::raw('count(distinct event_id) as total'))
            ->groupBy('division_id')
            ->pluck('total', 'division_id');

        $chartData = [
            'labels' => [],
            'registrant' => [],
            'instagram' => [],
            'tiktok' => []
        ];
        
        // Periode PMB: Oktober tahun sebelumnya s/d September tahun periode
        // Jika periode "2026", maka dimulai dari Okt 2025
        $periodYear = (int) $activePeriod->name;
        if ($periodYear < 2000) $periodYear = (int) date('Y');
        $startYear = $periodYear - 1;
        
        $periodMonths = [];
        $currentMonth = \Carbon\Carbon::createFromDate($startYear, 10, 1);
        for ($i = 0; $i < 12; $i++) {
            $periodMonths[] = $currentMonth->format('Y-m');
            $currentMonth->addMonth();
        }

        $rawMetrics = \App\Models\Metric::where('period_id', $activePeriod->id)->orderBy('date')->get();
        $groupedMetrics = $rawMetrics->groupBy(function($item) {
            return $item->date->format('Y-m');
        });

        // Dapatkan nilai terakhir sebelum periode ini dimulai agar grafik tidak mulai dari 0 jika sudah ada data
        $startDateStr = $startYear . '-10-01';
        $regBefore = $rawMetrics->where('type', 'registrant')->where('date', '<', $startDateStr)->sortByDesc('date')->first();
        $igBefore = $rawMetrics->where('type', 'instagram')->where('date', '<', $startDateStr)->sortByDesc('date')->first();
        $ttBefore = $rawMetrics->where('type', 'tiktok')->where('date', '<', $startDateStr)->sortByDesc('date')->first();

        $lastReg = $regBefore ? $regBefore->value : 0; 
        $lastIg = $igBefore ? $igBefore->value : 0; 
        $lastTt = $ttBefore ? $ttBefore->value : 0;

        foreach($periodMonths as $ym) {
            $monthLabel = \Carbon\Carbon::createFromFormat('Y-m-d', $ym . '-01')->translatedFormat('M Y');
            $chartData['labels'][] = $monthLabel;
            
            if ($groupedMetrics->has($ym)) {
                $items = $groupedMetrics->get($ym);
                
                $reg = $items->where('type', 'registrant')->sortByDesc('date')->first();
                $lastReg = $reg ? $reg->value : $lastReg;

                $ig = $items->where('type', 'instagram')->sortByDesc('date')->first();
                $lastIg = $ig ? $ig->value : $lastIg;

                $tt = $items->where('type', 'tiktok')->sortByDesc('date')->first();
                $lastTt = $tt ? $tt->value : $lastTt;
            }
            
            $chartData['registrant'][] = $lastReg;
            $chartData['instagram'][] = $lastIg;
            $chartData['tiktok'][] = $lastTt;
        }

        $igTotalLive = $rawMetrics->where('type', 'instagram_live')->count();
        $igTotalImpressions = $rawMetrics->where('type', 'instagram_live')->sum('value');
        
        $ttTotalLive = $rawMetrics->where('type', 'tiktok_live')->count();
        $ttTotalImpressions = $rawMetrics->where('type', 'tiktok_live')->sum('value');

        return view('pages.dashboard', compact(
            'settings', 'members', 'latestNews', 'divisions', 'divisionEventCounts', 'chartData',
            'igTotalLive', 'igTotalImpressions', 'ttTotalLive', 'ttTotalImpressions'
        ));
    }
}
