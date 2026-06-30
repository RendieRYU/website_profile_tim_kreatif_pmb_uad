@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center gap-4">
    <a href="{{ route('admin.categories.index') }}" class="text-slate-400 hover:text-blue-600 transition-colors">
        <i class="fas fa-arrow-left text-xl"></i>
    </a>
    <h1 class="text-3xl font-bold text-slate-800">Tambah Kategori</h1>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Kategori</label>
            <input type="text" name="name" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required placeholder="Contoh: Feeds, Live, Reels">
            @error('name') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Warna Kategori (Hex Code)</label>
            <div class="flex items-center space-x-3">
                <input type="color" name="color" class="h-10 w-10 border border-slate-300 rounded cursor-pointer" value="#cbd5e1" required>
                <span class="text-sm text-slate-500">Pilih warna yang akan digunakan sebagai label latar belakang tag kategori ini di Kalender.</span>
            </div>
            @error('color') <p class="text-sm text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan</button>
        </div>
    </form>
</div>
@endsection
