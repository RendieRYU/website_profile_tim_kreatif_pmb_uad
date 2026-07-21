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

        $divisionEvents = \App\Models\Event::where('events.period_id', $activePeriod->id)
            ->join('event_member', 'events.id', '=', 'event_member.event_id')
            ->select('events.id', 'events.title', 'events.event_date', 'events.status', 'event_member.division_id')
            ->distinct()
            ->orderBy('events.event_date', 'desc')
            ->get()
            ->groupBy('division_id');

        $chartData = [
            'labels' => [],
            'registrant' => [],
            'instagram' => [],
            'tiktok' => [],
            'instagram_impressions' => [],
            'tiktok_impressions' => []
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
            
            $igImpressions = 0;
            $ttImpressions = 0;

            if ($groupedMetrics->has($ym)) {
                $items = $groupedMetrics->get($ym);
                
                $reg = $items->where('type', 'registrant')->sortByDesc('date')->first();
                $lastReg = $reg ? $reg->value : $lastReg;

                $ig = $items->where('type', 'instagram')->sortByDesc('date')->first();
                $lastIg = $ig ? $ig->value : $lastIg;

                $tt = $items->where('type', 'tiktok')->sortByDesc('date')->first();
                $lastTt = $tt ? $tt->value : $lastTt;

                $igImpressions = $items->where('type', 'instagram_live')->sum('value');
                $ttImpressions = $items->where('type', 'tiktok_live')->sum('value');
            }
            
            $chartData['registrant'][] = $lastReg;
            $chartData['instagram'][] = $lastIg;
            $chartData['tiktok'][] = $lastTt;
            $chartData['instagram_impressions'][] = $igImpressions;
            $chartData['tiktok_impressions'][] = $ttImpressions;
        }

        $igTotalLive = $rawMetrics->where('type', 'instagram_live')->count();
        $igTotalImpressions = $rawMetrics->where('type', 'instagram_live')->sum('value');
        
        $ttTotalLive = $rawMetrics->where('type', 'tiktok_live')->count();
        $ttTotalImpressions = $rawMetrics->where('type', 'tiktok_live')->sum('value');

        $latestRegistrant = \App\Models\Metric::where('period_id', $activePeriod->id)
            ->where('type', 'registrant')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first()?->value ?? ($settings['registrant_count'] ?? 0);

        $latestInstagram = \App\Models\Metric::where('period_id', $activePeriod->id)
            ->where('type', 'instagram')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first()?->value ?? 0;

        $latestTiktok = \App\Models\Metric::where('period_id', $activePeriod->id)
            ->where('type', 'tiktok')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->first()?->value ?? 0;

        // Fetch and process Instagram Post data for sorting and charts
        $rawInstagramPosts = \App\Models\InstagramPost::where('period_id', $activePeriod->id)->orderBy('tanggal_posting')->get();
        
        $instagramMonthly = [];
        $instagramDaily = [];
        $yearlyLabels = [];
        $yearlyPosts = [];
        $yearlyFollowers = [];

        foreach($periodMonths as $ym) {
            $monthObj = \Carbon\Carbon::createFromFormat('Y-m-d', $ym . '-01');
            $monthLabel = $monthObj->translatedFormat('M Y');
            $yearlyLabels[] = $monthLabel;

            $monthPosts = $rawInstagramPosts->filter(function($post) use ($ym) {
                return $post->tanggal_posting->format('Y-m') === $ym;
            });

            $totalPosts = $monthPosts->count();
            $newFollowers = $monthPosts->sum('new_followers');

            $yearlyPosts[] = $totalPosts;
            $yearlyFollowers[] = $newFollowers;

            $instagramMonthly[$ym] = [
                'total_posts' => $totalPosts,
                'new_followers' => $newFollowers,
                'label' => $monthObj->translatedFormat('F Y')
            ];

            // Generate daily labels and metrics
            $daysInMonth = $monthObj->daysInMonth;
            $dailyLabels = [];
            $dailyPostCounts = [];
            $dailyFollowerCounts = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%s-%02d', $ym, $d);
                $dailyLabels[] = $d . ' ' . $monthObj->translatedFormat('M');

                $dayPosts = $monthPosts->filter(function($post) use ($dateStr) {
                    return $post->tanggal_posting->format('Y-m-d') === $dateStr;
                });

                $dailyPostCounts[] = $dayPosts->count();
                $dailyFollowerCounts[] = $dayPosts->sum('new_followers');
            }

            $instagramDaily[$ym] = [
                'labels' => $dailyLabels,
                'posts' => $dailyPostCounts,
                'followers' => $dailyFollowerCounts
            ];
        }

        // Hitung total keseluruhan untuk penayangan 'Seluruh Bulan'
        $grandTotalPosts = $rawInstagramPosts->count();
        $grandTotalNewFollowers = $rawInstagramPosts->sum('new_followers');

        // Fetch and process TikTok Post data for sorting and charts
        $rawTiktokPosts = \App\Models\TiktokPost::where('period_id', $activePeriod->id)->orderBy('tanggal_posting')->get();
        
        $tiktokMonthly = [];
        $tiktokDaily = [];
        $tiktokYearlyPosts = [];
        $tiktokYearlyFollowers = [];

        foreach($periodMonths as $ym) {
            $monthObj = \Carbon\Carbon::createFromFormat('Y-m-d', $ym . '-01');

            $monthPosts = $rawTiktokPosts->filter(function($post) use ($ym) {
                return $post->tanggal_posting->format('Y-m') === $ym;
            });

            $totalPosts = $monthPosts->count();
            $newFollowers = $monthPosts->sum('new_followers');

            $tiktokYearlyPosts[] = $totalPosts;
            $tiktokYearlyFollowers[] = $newFollowers;

            $tiktokMonthly[$ym] = [
                'total_posts' => $totalPosts,
                'new_followers' => $newFollowers,
                'label' => $monthObj->translatedFormat('F Y')
            ];

            // Generate daily labels and metrics
            $daysInMonth = $monthObj->daysInMonth;
            $dailyLabels = [];
            $dailyPostCounts = [];
            $dailyFollowerCounts = [];

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $dateStr = sprintf('%s-%02d', $ym, $d);
                $dailyLabels[] = $d . ' ' . $monthObj->translatedFormat('M');

                $dayPosts = $monthPosts->filter(function($post) use ($dateStr) {
                    return $post->tanggal_posting->format('Y-m-d') === $dateStr;
                });

                $dailyPostCounts[] = $dayPosts->count();
                $dailyFollowerCounts[] = $dayPosts->sum('new_followers');
            }

            $tiktokDaily[$ym] = [
                'labels' => $dailyLabels,
                'posts' => $dailyPostCounts,
                'followers' => $dailyFollowerCounts
            ];
        }

        $tiktokGrandTotalPosts = $rawTiktokPosts->count();
        $tiktokGrandTotalNewFollowers = $rawTiktokPosts->sum('new_followers');

        return view('pages.dashboard', compact(
            'settings', 'members', 'latestNews', 'divisions', 'divisionEventCounts', 'divisionEvents', 'chartData',
            'igTotalLive', 'igTotalImpressions', 'ttTotalLive', 'ttTotalImpressions',
            'latestRegistrant', 'latestInstagram', 'latestTiktok',
            'instagramMonthly', 'instagramDaily', 'yearlyLabels', 'yearlyPosts', 'yearlyFollowers',
            'grandTotalPosts', 'grandTotalNewFollowers',
            'tiktokMonthly', 'tiktokDaily', 'tiktokYearlyPosts', 'tiktokYearlyFollowers',
            'tiktokGrandTotalPosts', 'tiktokGrandTotalNewFollowers'
        ));
    }
}
