<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Period;

class PeriodController extends Controller
{
    public function index() { return view('admin.periods.index', ['periods' => Period::orderByDesc('name')->get()]); }
    public function create() { return view('admin.periods.create'); }
    public function store(Request $request) {
        $validated = $request->validate(['name' => 'required', 'is_active' => 'boolean']);
        $validated['is_active'] = $request->has('is_active');
        
        if ($validated['is_active']) {
            Period::query()->update(['is_active' => false]);
        }
        
        Period::create($validated);
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil ditambahkan.');
    }
    public function edit(Period $period) { return view('admin.periods.edit', compact('period')); }
    public function update(Request $request, Period $period) {
        $validated = $request->validate(['name' => 'required', 'is_active' => 'boolean']);
        $validated['is_active'] = $request->has('is_active');

        if ($validated['is_active']) {
            Period::where('id', '!=', $period->id)->update(['is_active' => false]);
        }

        $period->update($validated);
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil diperbarui.');
    }
    public function destroy(Period $period) {
        $period->delete();
        return redirect()->route('admin.periods.index')->with('success', 'Periode berhasil dihapus.');
    }
}
