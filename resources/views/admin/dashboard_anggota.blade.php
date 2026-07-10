@extends('layouts.admin')
@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-slate-800">Selamat Datang, {{ auth()->user()->username }}! 👋</h1>
    <p class="text-slate-500 mt-2">Ini adalah dashboard khusus untuk mengelola karya dan portofolio tim.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 flex items-center gap-4">
        <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl">
            <i class="fas fa-briefcase"></i>
        </div>
        <div>
            <p class="text-slate-500 text-sm font-medium">Total Karya Tersimpan</p>
            <h3 class="text-2xl font-bold text-slate-800">{{ collect($stats)->get('works', 0) }}</h3>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 text-center max-w-2xl mx-auto mt-12">
    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">
        <i class="fas fa-upload"></i>
    </div>
    <h2 class="text-xl font-bold text-slate-800 mb-2">Mulai Unggah Karya</h2>
    <p class="text-slate-500 mb-6">Unggah foto, video, atau design grafis yang telah dibuat oleh Anda atau tim. Kumpulkan portofolio untuk membangun rekam jejak yang baik.</p>
    <a href="{{ route('admin.works.create') }}" class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
        <i class="fas fa-plus mr-2"></i> Tambah Karya Baru
    </a>
</div>
@endsection
