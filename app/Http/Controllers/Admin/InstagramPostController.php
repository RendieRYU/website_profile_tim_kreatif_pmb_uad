<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstagramPost;
use App\Models\Period;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InstagramPostController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif. Silakan aktifkan salah satu periode terlebih dahulu.');
        }

        $query = InstagramPost::where('period_id', $activePeriod->id);

        // Filter berdasarkan bulan (format: Y-m)
        if ($request->has('month') && $request->month != '') {
            $date = Carbon::parse($request->month . '-01');
            $query->whereYear('tanggal_posting', $date->year)
                  ->whereMonth('tanggal_posting', $date->month);
        }

        $posts = $query->orderBy('tanggal_posting', 'desc')->get();

        // Dapatkan semua bulan unik dari postingan untuk filter dropdown
        $allMonths = [];
        $periodYear = (int) $activePeriod->name;
        if ($periodYear < 2000) $periodYear = (int) date('Y');
        $startYear = $periodYear - 1;
        $currentMonth = Carbon::createFromDate($startYear, 10, 1);
        for ($i = 0; $i < 12; $i++) {
            $allMonths[$currentMonth->format('Y-m')] = $currentMonth->translatedFormat('F Y');
            $currentMonth->addMonth();
        }

        $customColumns = Setting::get('instagram_custom_columns', []);
        if (is_string($customColumns)) {
            $customColumns = json_decode($customColumns, true) ?? [];
        }

        $defaultColumns = [
            ['id' => 'tanggal_posting', 'name' => 'Tanggal', 'type' => 'standard', 'width' => 'min-w-[130px]'],
            ['id' => 'jenis_konten', 'name' => 'Jenis', 'type' => 'standard', 'width' => 'min-w-[100px]'],
            ['id' => 'pillar', 'name' => 'Pillar', 'type' => 'standard', 'width' => 'min-w-[130px]'],
            ['id' => 'likes', 'name' => 'Likes', 'type' => 'standard', 'width' => 'w-20'],
            ['id' => 'comments', 'name' => 'Comm.', 'type' => 'standard', 'width' => 'w-20'],
            ['id' => 'shares', 'name' => 'Shares', 'type' => 'standard', 'width' => 'w-20'],
            ['id' => 'saves', 'name' => 'Saves', 'type' => 'standard', 'width' => 'w-20'],
            ['id' => 'reposts', 'name' => 'Reposts', 'type' => 'standard', 'width' => 'w-20'],
            ['id' => 'total_engagement', 'name' => 'Total Eng.', 'type' => 'standard', 'width' => 'w-24'],
            ['id' => 'reach', 'name' => 'Reach', 'type' => 'standard', 'width' => 'w-24'],
            ['id' => 'new_followers', 'name' => 'Followers +', 'type' => 'standard', 'width' => 'w-24'],
            ['id' => 'er_by_post', 'name' => 'ER Post', 'type' => 'standard', 'width' => 'w-24'],
            ['id' => 'er_by_reach', 'name' => 'ER Reach', 'type' => 'standard', 'width' => 'w-24'],
            ['id' => 'link_konten', 'name' => 'Link Post', 'type' => 'standard', 'width' => 'min-w-[140px]'],
        ];

        $allColumns = $defaultColumns;
        foreach ($customColumns as $col) {
            $allColumns[] = [
                'id' => $col['id'],
                'name' => $col['name'],
                'type' => 'custom',
                'col_type' => $col['type'] ?? 'text',
                'options' => $col['options'] ?? '',
                'width' => 'min-w-[150px]'
            ];
        }

        $columnOrder = Setting::get('instagram_table_columns_order', []);
        if (is_string($columnOrder)) {
            $columnOrder = json_decode($columnOrder, true) ?? [];
        }

        $orderedColumns = [];
        if (!empty($columnOrder)) {
            $columnMap = collect($allColumns)->keyBy('id');
            foreach ($columnOrder as $colId) {
                if ($columnMap->has($colId)) {
                    $orderedColumns[] = $columnMap->get($colId);
                    $columnMap->forget($colId);
                }
            }
            foreach ($columnMap as $col) {
                $orderedColumns[] = $col;
            }
        } else {
            $orderedColumns = $allColumns;
        }
        $tableColumns = $orderedColumns;

        return view('admin.instagram_posts.index', compact('posts', 'activePeriod', 'allMonths', 'customColumns', 'tableColumns'));
    }

    public function quickStore(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return response()->json(['success' => false, 'message' => 'Belum ada periode yang aktif.'], 422);
        }

        $validated = $request->validate([
            'tanggal_posting' => 'required|date',
            'link_konten' => 'nullable',
            'jenis_konten' => 'required|in:REELS,FEED,STORY,OTHERS',
            'pillar' => 'nullable|string|max:255',
            'likes' => 'nullable|integer|min:0',
            'comments' => 'nullable|integer|min:0',
            'shares' => 'nullable|integer|min:0',
            'saves' => 'nullable|integer|min:0',
            'reposts' => 'nullable|integer|min:0',
            'reach' => 'nullable|integer|min:0',
            'new_followers' => 'nullable|integer|min:0',
            'custom_fields' => 'nullable|array'
        ]);

        $validated['period_id'] = $activePeriod->id;
        foreach (['likes', 'comments', 'shares', 'saves', 'reposts', 'reach', 'new_followers'] as $f) {
            $validated[$f] = $validated[$f] ?? 0;
        }
        $validated['custom_fields'] = $validated['custom_fields'] ?? [];

        $post = InstagramPost::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Postingan Instagram berhasil ditambahkan ke spreadsheet!',
            'post' => $post->fresh()
        ]);
    }

    public function inlineUpdate(Request $request, InstagramPost $instagramPost)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        if ($field === 'custom_field') {
            $colId = $request->input('column_id');
            $fields = $instagramPost->custom_fields ?? [];
            $fields[$colId] = $value;
            $instagramPost->custom_fields = $fields;
            $instagramPost->save();

            return response()->json([
                'success' => true,
                'message' => 'Kolom kustom berhasil diperbarui.',
                'post' => $instagramPost->fresh()
            ]);
        }

        $allowedFields = ['tanggal_posting', 'link_konten', 'jenis_konten', 'pillar', 'reach', 'likes', 'comments', 'shares', 'saves', 'reposts', 'new_followers'];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['success' => false, 'message' => 'Kolom tidak valid.'], 422);
        }

        if (in_array($field, ['reach', 'likes', 'comments', 'shares', 'saves', 'reposts', 'new_followers'])) {
            $value = max(0, (int) $value);
        }

        $instagramPost->$field = $value;
        $instagramPost->save();

        return response()->json([
            'success' => true,
            'message' => 'Data postingan berhasil diperbarui.',
            'post' => $instagramPost->fresh(),
            'total_engagement' => $instagramPost->total_engagement,
            'er_by_post' => $instagramPost->er_by_post,
            'er_by_reach' => $instagramPost->er_by_reach,
        ]);
    }

    public function addColumn(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'nullable|string|in:text,dropdown,checklist,date',
            'options' => 'nullable|string|max:500'
        ]);

        $columns = Setting::get('instagram_custom_columns', []);
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }

        $newCol = [
            'id' => 'ig_col_' . time() . '_' . rand(10, 99),
            'name' => trim($request->name),
            'type' => $request->type ?? 'text',
            'options' => $request->type === 'dropdown' ? trim($request->options ?? '') : ''
        ];

        $columns[] = $newCol;
        Setting::set('instagram_custom_columns', json_encode($columns));

        return response()->json([
            'success' => true,
            'message' => 'Kolom baru "' . $newCol['name'] . '" berhasil ditambahkan ke spreadsheet!',
            'columns' => $columns,
            'new_column' => $newCol
        ]);
    }

    public function deleteColumn(Request $request, $columnId)
    {
        $columns = Setting::get('instagram_custom_columns', []);
        if (is_string($columns)) {
            $columns = json_decode($columns, true) ?? [];
        }

        $columns = array_values(array_filter($columns, function($col) use ($columnId) {
            return ($col['id'] ?? '') !== $columnId;
        }));

        Setting::set('instagram_custom_columns', json_encode($columns));

        $columnOrder = Setting::get('instagram_table_columns_order', []);
        if (is_string($columnOrder)) {
            $columnOrder = json_decode($columnOrder, true) ?? [];
        }
        if (is_array($columnOrder)) {
            $columnOrder = array_values(array_filter($columnOrder, function($id) use ($columnId) {
                return $id !== $columnId;
            }));
            Setting::set('instagram_table_columns_order', json_encode($columnOrder));
        }

        return response()->json([
            'success' => true,
            'message' => 'Kolom kustom berhasil dihapus!',
            'columns' => $columns
        ]);
    }

    public function updateColumnOrder(Request $request)
    {
        $request->validate([
            'columns' => 'required|array',
            'columns.*' => 'string'
        ]);

        Setting::set('instagram_table_columns_order', json_encode($request->columns));

        return response()->json([
            'success' => true,
            'message' => 'Urutan kolom berhasil disimpan!'
        ]);
    }

    public function create()
    {
        return view('admin.instagram_posts.create');
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
            'jenis_konten' => 'required|in:REELS,FEED,STORY,OTHERS',
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

        InstagramPost::create($validated);

        return redirect()->route('admin.instagram-posts.index')->with('success', 'Laporan postingan Instagram berhasil ditambahkan.');
    }

    public function edit(InstagramPost $instagramPost)
    {
        return view('admin.instagram_posts.edit', compact('instagramPost'));
    }

    public function update(Request $request, InstagramPost $instagramPost)
    {
        $validated = $request->validate([
            'tanggal_posting' => 'required|date',
            'link_konten' => 'nullable|url',
            'jenis_konten' => 'required|in:REELS,FEED,STORY,OTHERS',
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

        $instagramPost->update($validated);

        return redirect()->route('admin.instagram-posts.index')->with('success', 'Laporan postingan Instagram berhasil diperbarui.');
    }

    public function destroy(InstagramPost $instagramPost)
    {
        $instagramPost->delete();
        return redirect()->route('admin.instagram-posts.index')->with('success', 'Laporan postingan Instagram berhasil dihapus.');
    }

    public function export(Request $request)
    {
        $activePeriod = Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Belum ada periode aktif untuk ekspor.');
        }

        $query = InstagramPost::where('period_id', $activePeriod->id);

        $monthName = 'Semua_Bulan';
        if ($request->has('month') && $request->month != '') {
            $date = Carbon::parse($request->month . '-01');
            $query->whereYear('tanggal_posting', $date->year)
                  ->whereMonth('tanggal_posting', $date->month);
            $monthName = $date->format('F_Y');
        }

        $posts = $query->orderBy('tanggal_posting', 'asc')->get();

        $customColumns = Setting::get('instagram_custom_columns', []);
        if (is_string($customColumns)) {
            $customColumns = json_decode($customColumns, true) ?? [];
        }

        $filename = "Laporan_Instagram_{$activePeriod->name}_{$monthName}.xls";

        return response(view('admin.instagram_posts.excel', compact('posts', 'customColumns'))->render(), 200, [
            "Content-Type" => "application/vnd.ms-excel; charset=utf-8",
            "Content-Disposition" => "attachment; filename=\"{$filename}\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
