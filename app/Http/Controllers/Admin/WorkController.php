<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Member;

class WorkController extends Controller
{
    public function index()
    {
        $works = Work::latest('work_date')->paginate(10);
        return view('admin.works.index', compact('works'));
    }

    public function create()
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        return view('admin.works.create', compact('members'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'work_type' => 'required|in:Foto,Video,Design Grafis',
            'work_date' => 'required|date',
            'link' => 'nullable|url',
        ]);

        $work = Work::create($validated);

        if ($request->has('members')) {
            $work->members()->sync($request->members);
        }

        return redirect()->route('admin.works.index')->with('success', 'Karya berhasil ditambahkan.');
    }

    public function edit(Work $work)
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        $members = $activePeriod ? Member::with('division')->where('period_id', $activePeriod->id)->get() : collect();
        return view('admin.works.edit', compact('work', 'members'));
    }

    public function update(Request $request, Work $work)
    {
        $validated = $request->validate([
            'title' => 'required',
            'work_type' => 'required|in:Foto,Video,Design Grafis',
            'work_date' => 'required|date',
            'link' => 'nullable|url',
        ]);

        $work->update($validated);

        if ($request->has('members')) {
            $work->members()->sync($request->members);
        } else {
            $work->members()->detach();
        }

        return redirect()->route('admin.works.index')->with('success', 'Karya berhasil diperbarui.');
    }

    public function destroy(Work $work)
    {
        $work->delete();
        return redirect()->route('admin.works.index')->with('success', 'Karya berhasil dihapus.');
    }
}
