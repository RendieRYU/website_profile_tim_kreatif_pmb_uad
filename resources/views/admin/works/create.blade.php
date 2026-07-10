@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Tambah Karya / Portofolio</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.works.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Karya</label>
                <input type="text" name="title" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Karya</label>
                <select name="work_type" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">Pilih Jenis...</option>
                    <option value="Foto">Foto</option>
                    <option value="Video">Video</option>
                    <option value="Design Grafis">Design Grafis</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Publikasi/Pembuatan</label>
                <input type="date" name="work_date" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Tautan Karya (Link Google Drive, YouTube, dll)</label>
                <input type="url" name="link" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Anggota Pembuat</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3 max-h-60 overflow-y-auto p-4 border border-slate-200 rounded-lg bg-slate-50">
                    @foreach($members as $member)
                    <label class="flex items-center space-x-3 bg-white p-2 border border-slate-100 rounded shadow-sm hover:bg-blue-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="members[]" value="{{ $member->id }}" class="form-checkbox h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <span class="text-slate-700 text-sm font-medium">{{ $member->full_name }} <span class="text-xs text-slate-400">({{ $member->division->name ?? '-' }})</span></span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-8">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Karya</button>
            <a href="{{ route('admin.works.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
        </div>
    </form>
</div>
@endsection
