@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Tambah Divisi</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.divisions.store') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Divisi</label>
            <input type="text" name="name" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Warna (Hex)</label>
            <input type="color" name="color_hex" class="w-full h-12 p-1 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 cursor-pointer" required>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
        <a href="{{ route('admin.divisions.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
    </form>
</div>
@endsection
