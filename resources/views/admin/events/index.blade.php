@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Kegiatan</h1>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <a href="{{ route('admin.events.export') }}" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
            <i class="fas fa-file-excel"></i> Ekspor Excel
        </a>
        <a href="{{ route('admin.events.create') }}" class="w-full sm:w-auto px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
            <i class="fas fa-plus"></i> Tambah Kegiatan
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">ID</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Judul Kegiatan</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Tanggal</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Petugas</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($events as $event)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">{{ $events->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-3 md:p-4 font-bold text-slate-800 text-sm">{{ $event->title }}</td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">
                        <i class="far fa-calendar-alt mr-1"></i> {{ $event->event_date->format('d M Y') }}<br>
                        <i class="far fa-clock mr-1 text-slate-400"></i> {{ $event->event_time ? $event->event_time->format('H:i') : '-' }}
                    </td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">
                        <span class="px-2 py-1 bg-slate-100 rounded-lg text-xs">{{ $event->members->count() }} Anggota</span>
                    </td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm">
                        <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-500 hover:text-blue-700 inline-block"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data kegiatan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">
        {{ $events->links() }}
    </div>
</div>
@endsection
