<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TiktokPost;
use App\Models\Period;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TiktokPostController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif. Silakan aktifkan salah satu periode terlebih dahulu.');
        }

        $query = TiktokPost::where('period_id', $activePeriod->id);

        if ($request->has('month') && $request->month != '') {
            $date = Carbon::parse($request->month . '-01');
            $query->whereYear('tanggal_posting', $date->year)
                  ->whereMonth('tanggal_posting', $date->month);
        }

        $posts = $query->orderBy('tanggal_posting', 'desc')->paginate(15);

        $allMonths = [];
        $periodYear = (int) $activePeriod->name;
        if ($periodYear < 2000) $periodYear = (int) date('Y');
        $startYear = $periodYear - 1;
        $currentMonth = Carbon::createFromDate($startYear, 10, 1);
        for ($i = 0; $i < 12; $i++) {
            $allMonths[$currentMonth->format('Y-m')] = $currentMonth->translatedFormat('F Y');
            $currentMonth->addMonth();
        }

        return view('admin.tiktok_posts.index', compact('posts', 'activePeriod', 'allMonths'));
    }

    public function create()
    {
        return view('admin.tiktok_posts.create');
    }

    public function store(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan: belum ada periode aktif.');
        }

        $validated = $request->validate([
            'tanggal_posting' => 'required|date',
            'link_konten' => 'nullable|url',
            'jenis_konten' => 'required|in:VIDEO,STORY,LIVE,OTHERS',
            'pillar' => 'nullable|string|max:255',
            'reach' => 'nullable|integer|min:0',
            'impression_home' => 'nullable|integer|min:0',
            'impression_profile' => 'nullable|integer|min:0',
            'impression_explore' => 'nullable|integer|min:0',
            'impression_hashtag' => 'nullable|integer|min:0',
            'impression_other' => 'nullable|integer|min:0',
            'interaction_followers' => 'nullable|numeric|between:0,100',
            'interaction_non_followers' => 'nullable|numeric|between:0,100',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'saves' => 'nullable|integer|min:0',
            'reposts' => 'nullable|integer|min:0',
            'new_followers' => 'nullable|integer|min:0',
        ]);

        $validated['period_id'] = $activePeriod->id;

        foreach (['reach', 'impression_home', 'impression_profile', 'impression_explore', 'impression_hashtag', 'impression_other', 'likes', 'comments', 'shares', 'saves', 'reposts', 'new_followers'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }
        $validated['interaction_followers'] = $validated['interaction_followers'] ?? 0.0;
        $validated['interaction_non_followers'] = $validated['interaction_non_followers'] ?? 0.0;

        TiktokPost::create($validated);

        return redirect()->route('admin.tiktok-posts.index')->with('success', 'Laporan postingan TikTok berhasil ditambahkan.');
    }

    public function edit(TiktokPost $tiktokPost)
    {
        return view('admin.tiktok_posts.edit', compact('tiktokPost'));
    }

    public function update(Request $request, TiktokPost $tiktokPost)
    {
        $validated = $request->validate([
            'tanggal_posting' => 'required|date',
            'link_konten' => 'nullable|url',
            'jenis_konten' => 'required|in:VIDEO,STORY,LIVE,OTHERS',
            'pillar' => 'nullable|string|max:255',
            'reach' => 'nullable|integer|min:0',
            'impression_home' => 'nullable|integer|min:0',
            'impression_profile' => 'nullable|integer|min:0',
            'impression_explore' => 'nullable|integer|min:0',
            'impression_hashtag' => 'nullable|integer|min:0',
            'impression_other' => 'nullable|integer|min:0',
            'interaction_followers' => 'nullable|numeric|between:0,100',
            'interaction_non_followers' => 'nullable|numeric|between:0,100',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'saves' => 'nullable|integer|min:0',
            'reposts' => 'nullable|integer|min:0',
            'new_followers' => 'nullable|integer|min:0',
        ]);

        foreach (['reach', 'impression_home', 'impression_profile', 'impression_explore', 'impression_hashtag', 'impression_other', 'likes', 'comments', 'shares', 'saves', 'reposts', 'new_followers'] as $field) {
            $validated[$field] = $validated[$field] ?? 0;
        }
        $validated['interaction_followers'] = $validated['interaction_followers'] ?? 0.0;
        $validated['interaction_non_followers'] = $validated['interaction_non_followers'] ?? 0.0;

        $tiktokPost->update($validated);

        return redirect()->route('admin.tiktok-posts.index')->with('success', 'Laporan postingan TikTok berhasil diperbarui.');
    }

    public function destroy(TiktokPost $tiktokPost)
    {
        $tiktokPost->delete();
        return redirect()->route('admin.tiktok-posts.index')->with('success', 'Laporan postingan TikTok berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Belum ada periode aktif untuk ekspor.');
        }

        $query = TiktokPost::where('period_id', $activePeriod->id);

        $monthName = 'Semua_Bulan';
        if ($request->has('month') && $request->month != '') {
            $date = Carbon::parse($request->month . '-01');
            $query->whereYear('tanggal_posting', $date->year)
                  ->whereMonth('tanggal_posting', $date->month);
            $monthName = $date->format('F_Y');
        }

        $posts = $query->orderBy('tanggal_posting', 'asc')->get();

        $filename = "Laporan_TikTok_{$activePeriod->name}_{$monthName}.xls";

        return response(view('admin.tiktok_posts.excel', compact('posts'))->render(), 200, [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
