@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Manajemen Divisi</h1>
    <a href="{{ route('admin.divisions.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Tambah Divisi</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="p-4 font-medium text-slate-600">ID</th>
                <th class="p-4 font-medium text-slate-600">Nama Divisi</th>
                <th class="p-4 font-medium text-slate-600">Warna Legend</th>
                <th class="p-4 font-medium text-slate-600 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($divisions as $division)
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="p-4 text-slate-600">{{ $division->id }}</td>
                <td class="p-4 font-bold text-slate-800">{{ $division->name }}</td>
                <td class="p-4">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-full" style="background-color: {{ $division->color_hex }}"></div>
                        <span class="text-slate-600">{{ $division->color_hex }}</span>
                    </div>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.divisions.edit', $division) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-8 text-center text-slate-500">Belum ada data divisi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
