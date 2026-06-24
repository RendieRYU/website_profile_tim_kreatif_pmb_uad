@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Edit Periode</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.periods.update', $period) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Tahun Periode</label>
            <input type="text" name="name" value="{{ $period->name }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        <div class="mb-6 flex items-center">
            <input type="checkbox" name="is_active" value="1" id="is_active" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" {{ $period->is_active ? 'checked' : '' }}>
            <label for="is_active" class="ml-2 block text-sm text-gray-900">Jadikan Periode Aktif</label>
        </div>
        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan Perubahan</button>
        <a href="{{ route('admin.periods.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
    </form>
</div>
@endsection
