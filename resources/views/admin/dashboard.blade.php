@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Dashboard Admin</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 font-medium">Total Anggota</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['members'] }}</p>
        </div>
        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 font-medium">Total Kegiatan</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['events'] }}</p>
        </div>
        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-calendar-check"></i>
        </div>
    </div>
    <div class="bg-white p-6 rounded-xl shadow-sm border border-slate-100 flex items-center justify-between">
        <div>
            <p class="text-slate-500 font-medium">Total Berita / Karya</p>
            <p class="text-3xl font-bold text-slate-800">{{ $stats['news'] }}</p>
        </div>
        <div class="w-12 h-12 bg-pink-100 text-pink-600 rounded-lg flex items-center justify-center text-xl">
            <i class="fas fa-newspaper"></i>
        </div>
    </div>
</div>
@endsection
