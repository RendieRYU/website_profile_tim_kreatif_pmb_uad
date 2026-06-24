@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Edit Anggota</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.members.update', $member) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" value="{{ $member->full_name }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Panggilan (Opsional)</label>
                <input type="text" name="nickname" value="{{ $member->nickname }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Divisi</label>
                <select name="division_id" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}" {{ $member->division_id == $division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode Aktif</label>
                <select name="period_id" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}" {{ $member->period_id == $period->id ? 'selected' : '' }}>{{ $period->name }} {{ $period->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Peran / Jabatan (Opsional)</label>
                <input type="text" name="role" value="{{ $member->role }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Profile</label>
                @if($member->photo)
                    <img src="{{ asset('storage/'.$member->photo) }}" class="w-16 h-16 rounded-lg mb-2 object-cover">
                @endif
                <input type="file" name="photo" class="w-full p-2 border border-slate-300 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Instagram (Link)</label>
                <input type="url" name="instagram" value="{{ $member->instagram }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">LinkedIn (Link)</label>
                <input type="url" name="linkedin" value="{{ $member->linkedin }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">GitHub (Link)</label>
                <input type="url" name="github" value="{{ $member->github }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="mt-8">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
            <a href="{{ route('admin.members.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
        </div>
    </form>
</div>
@endsection
