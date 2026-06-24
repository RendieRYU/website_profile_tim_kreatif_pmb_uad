@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Manajemen Anggota</h1>
    <a href="{{ route('admin.members.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Tambah Anggota</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="p-4 font-medium text-slate-600">ID</th>
                <th class="p-4 font-medium text-slate-600">Foto</th>
                <th class="p-4 font-medium text-slate-600">Nama Lengkap</th>
                <th class="p-4 font-medium text-slate-600">Divisi</th>
                <th class="p-4 font-medium text-slate-600 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="p-4 text-slate-600">{{ $member->id }}</td>
                <td class="p-4">
                    @if($member->photo)
                        <img src="{{ asset('storage/'.$member->photo) }}" class="w-10 h-10 rounded-full object-cover">
                    @else
                        <div class="w-10 h-10 rounded-full bg-slate-200 flex items-center justify-center text-slate-500"><i class="fas fa-user"></i></div>
                    @endif
                </td>
                <td class="p-4 font-bold text-slate-800">{{ $member->full_name }}<br><span class="text-sm font-normal text-slate-500">{{ $member->role }}</span></td>
                <td class="p-4">
                    @if($member->division)
                        <span class="px-2 py-1 rounded-full text-xs font-medium text-white" style="background-color: {{ $member->division->color_hex }}">{{ $member->division->name }}</span>
                    @else
                        <span class="text-slate-400">-</span>
                    @endif
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('admin.members.edit', $member) }}" class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.members.destroy', $member) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data anggota.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-100">
        {{ $members->links() }}
    </div>
</div>
@endsection
