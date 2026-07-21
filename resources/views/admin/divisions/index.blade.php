@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Divisi</h1>
    <a href="{{ route('admin.divisions.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-semibold text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah Divisi
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">ID</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Nama Divisi</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Warna Legend</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($divisions as $division)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-3 py-3 md:p-4 font-bold text-slate-800 text-sm">{{ $division->name }}</td>
                    <td class="px-3 py-3 md:p-4 text-sm">
                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <div class="w-5 h-5 rounded-full border border-slate-200" style="background-color: {{ $division->color_hex }}"></div>
                            <span class="text-slate-600 font-mono">{{ $division->color_hex }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm whitespace-nowrap">
                        <a href="{{ route('admin.divisions.edit', $division) }}" class="text-blue-500 hover:text-blue-700 inline-block"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.divisions.destroy', $division) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
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
</div>
@endsection
