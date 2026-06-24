<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Member;
use Barryvdh\DomPDF\Facade\Pdf;

class PortfolioController extends Controller
{
    public function index()
    {
        $periods = \App\Models\Period::with(['members.division'])->orderByDesc('name')->get();
        return view('pages.portfolio.index', compact('periods'));
    }

    public function show($slug)
    {
        // Temukan member berdasarkan slug. Karena slug adalah accessor, 
        // kita bisa mengambil semua member lalu mem-filternya (kurang ideal untuk big data),
        // tapi untuk tim kecil ini sangat aman, ATAU bisa juga query berdasarkan format slug ke nama:
        $member = Member::get()->first(function($m) use ($slug) {
            return $m->slug === $slug;
        });

        if (!$member) {
            return redirect()->route('portfolio.index')->with('error', 'Anggota tidak ditemukan.');
        }

        // Load relations
        $member->load(['events', 'news', 'division']);

        return view('pages.portfolio.show', compact('member'));
    }

    public function exportPdf($id)
    {
        $member = Member::with(['events', 'news', 'division'])->findOrFail($id);

        $pdf = Pdf::loadView('pages.portfolio.pdf', compact('member'));
        return $pdf->download('portofolio-' . \Illuminate\Support\Str::slug($member->full_name) . '.pdf');
    }
}
