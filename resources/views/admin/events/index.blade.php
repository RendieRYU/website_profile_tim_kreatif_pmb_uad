@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Manajemen Kegiatan</h1>
    <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Tambah Kegiatan</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="p-4 font-medium text-slate-600">ID</th>
                <th class="p-4 font-medium text-slate-600">Judul Kegiatan</th>
                <th class="p-4 font-medium text-slate-600">Tanggal</th>
                <th class="p-4 font-medium text-slate-600">Petugas</th>
                <th class="p-4 font-medium text-slate-600 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($events as $event)
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="p-4 text-slate-600">{{ $events->firstItem() + $loop->index }}</td>
                <td class="p-4 font-bold text-slate-800">{{ $event->title }}</td>
                <td class="p-4 text-slate-600">
                    <i class="far fa-calendar-alt mr-1"></i> {{ $event->event_date->format('d M Y') }}<br>
                    <span class="text-xs text-slate-500"><i class="far fa-clock mr-1"></i> {{ $event->event_time ? $event->event_time->format('H:i') : 'Seharian' }}</span>
                </td>
                <td class="p-4 text-slate-600">
                    <span class="px-2 py-1 bg-slate-100 rounded-lg text-xs">{{ $event->members->count() }} Anggota</span>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
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
    <div class="p-4 border-t border-slate-100">
        {{ $events->links() }}
    </div>
</div>
@endsection
