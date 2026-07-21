@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-8">Dashboard Admin</h1>

<!-- Statistik Keseluruhan Section -->
<div class="mb-8">
    <h2 class="text-xl font-bold text-slate-700 mb-4 flex items-center gap-2">
        <i class="fas fa-globe text-blue-500"></i> Statistik Keseluruhan (Semua Periode)
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Total Anggota</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['total_members'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Total Kegiatan</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['total_events'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Total Berita / Karya</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['total_news'] }}</p>
            </div>
            <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-newspaper"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistik Periode Aktif Section -->
<div>
    <h2 class="text-xl font-bold text-slate-700 mb-4 flex items-center gap-2">
        <i class="fas fa-history text-indigo-500"></i> Statistik Periode Saat Ini ({{ $stats['period_name'] }})
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Anggota Periode {{ $stats['period_name'] }}</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['period_members'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-500 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-user-friends"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Kegiatan Periode {{ $stats['period_name'] }}</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['period_events'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-500 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
            <div>
                <p class="text-slate-500 font-medium">Berita Periode {{ $stats['period_name'] }}</p>
                <p class="text-3xl font-bold text-slate-800">{{ $stats['period_news'] }}</p>
            </div>
            <div class="w-12 h-12 bg-pink-50 text-pink-500 rounded-lg flex items-center justify-center text-xl">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>
    </div>
</div>
@endsection
