@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Periode</h1>
    <a href="{{ route('admin.periods.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-semibold text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah Periode
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">ID</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Tahun Periode</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Status Aktif</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($periods as $period)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-3 py-3 md:p-4 font-bold text-slate-800 text-sm">{{ $period->name }}</td>
                    <td class="px-3 py-3 md:p-4 text-sm whitespace-nowrap">
                        @if($period->is_active)
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">Aktif</span>
                        @else
                            <span class="px-2 py-1 bg-slate-100 text-slate-700 rounded-full text-xs font-bold">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm whitespace-nowrap">
                        <a href="{{ route('admin.periods.edit', $period) }}" class="text-blue-500 hover:text-blue-700 inline-block"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.periods.destroy', $period) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">Belum ada data periode.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
