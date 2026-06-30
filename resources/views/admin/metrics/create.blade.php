@extends('layouts.admin')
@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.metrics.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
        <i class="fas fa-arrow-left text-xl"></i>
    </a>
    <h1 class="text-3xl font-bold text-slate-800">Tambah Data Statistik</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.metrics.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Statistik</label>
            <select name="type" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                <option value="registrant">Pendaftar UAD</option>
                <option value="instagram">Followers/Engagement Instagram</option>
                <option value="tiktok">Views/Engagement TikTok</option>
                <option value="instagram_live">Live Instagram (Penonton)</option>
                <option value="tiktok_live">Live TikTok (Penonton)</option>
            </select>
            @error('type') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="date" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required value="{{ date('Y-m-d') }}">
            <p class="text-xs text-slate-500 mt-1">Saran: Input data secara periodik (misal setiap tanggal 1 atau setiap akhir bulan) agar grafik lebih rapi.</p>
            @error('date') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nilai / Jumlah</label>
            <input type="number" name="value" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required min="0">
            @error('value') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan Data</button>
        </div>
    </form>
</div>
@endsection
