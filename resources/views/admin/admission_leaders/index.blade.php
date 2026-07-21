@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Manajemen Pimpinan Admisi</h1>
    <a href="{{ route('admin.admission-leaders.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Tambah Pimpinan</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="p-4 font-medium text-slate-600">ID</th>
                    <th class="p-4 font-medium text-slate-600">Foto</th>
                    <th class="p-4 font-medium text-slate-600">Nama Lengkap</th>
                    <th class="p-4 font-medium text-slate-600">Jabatan</th>
                    <th class="p-4 font-medium text-slate-600">Urutan Tampilan</th>
                    <th class="p-4 font-medium text-slate-600 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leaders as $leader)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-slate-600">{{ $loop->iteration }}</td>
                    <td class="p-4">
                        @if($leader->photo)
                            <img src="{{ asset('storage/'.$leader->photo) }}" class="w-10 h-10 rounded-lg object-cover">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user-tie"></i></div>
                        @endif
                    </td>
                    <td class="p-4 font-bold text-slate-800">{{ $leader->name }}</td>
                    <td class="p-4 text-slate-600">{{ $leader->position }}</td>
                    <td class="p-4 text-slate-600">{{ $leader->order }}</td>
                    <td class="p-4 text-right space-x-2">
                        <a href="{{ route('admin.admission-leaders.edit', $leader->id) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.admission-leaders.destroy', $leader->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pimpinan ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="p-8 text-center text-slate-500">Belum ada data pimpinan admisi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
