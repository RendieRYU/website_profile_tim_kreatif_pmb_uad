<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Metric;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function index(Request $request)
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        
        if (!$activePeriod) {
            return redirect()->route('admin.dashboard')->with('error', 'Belum ada periode yang aktif. Silakan aktifkan salah satu periode terlebih dahulu.');
        }

        $query = Metric::where('period_id', $activePeriod->id);

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $metrics = $query->orderBy('date', 'desc')->paginate(10);
        
        return view('admin.metrics.index', compact('metrics'));
    }

    public function create()
    {
        return view('admin.metrics.create');
    }

    public function store(Request $request)
    {
        $activePeriod = \App\Models\Period::where('is_active', true)->first();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Tidak dapat menambahkan statistik: belum ada periode aktif.');
        }

        $validated = $request->validate([
            'type' => 'required|in:registrant,instagram,tiktok,instagram_live,tiktok_live',
            'date' => 'required|date',
            'value' => 'required|integer|min:0',
        ]);

        $validated['period_id'] = $activePeriod->id;

        if ($validated['type'] === 'registrant') {
            $higherBefore = Metric::where('period_id', $activePeriod->id)
                ->where('type', 'registrant')
                ->where('date', '<=', $validated['date'])
                ->where('value', '>', $validated['value'])
                ->orderBy('date', 'desc')
                ->first();
                
            if ($higherBefore) {
                return redirect()->back()->withInput()->withErrors([
                    'value' => 'Jumlah pendaftar tidak boleh kurang dari data sebelumnya tanggal ' . $higherBefore->date->format('d M Y') . ' (' . $higherBefore->value . ').'
                ]);
            }

            $lowerAfter = Metric::where('period_id', $activePeriod->id)
                ->where('type', 'registrant')
                ->where('date', '>=', $validated['date'])
                ->where('value', '<', $validated['value'])
                ->orderBy('date', 'asc')
                ->first();

            if ($lowerAfter) {
                return redirect()->back()->withInput()->withErrors([
                    'value' => 'Jumlah pendaftar tidak boleh lebih dari data setelahnya tanggal ' . $lowerAfter->date->format('d M Y') . ' (' . $lowerAfter->value . ').'
                ]);
            }
        }

        Metric::create($validated);

        return redirect()->route('admin.metrics.index')->with('success', 'Statistik berhasil ditambahkan.');
    }

    public function destroy(Metric $metric)
    {
        $metric->delete();
        return redirect()->route('admin.metrics.index')->with('success', 'Statistik berhasil dihapus.');
    }
}
