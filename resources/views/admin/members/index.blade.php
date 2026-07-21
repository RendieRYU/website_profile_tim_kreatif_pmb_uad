@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Anggota</h1>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
        <form action="{{ route('admin.members.index') }}" method="GET" class="flex items-center gap-2 w-full sm:w-auto">
            <label for="sort" class="text-sm font-medium text-slate-600 shrink-0">Urutkan:</label>
            <select name="sort" id="sort" onchange="this.form.submit()" class="w-full sm:w-auto px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:border-blue-500">
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama (A-Z)</option>
                <option value="division" {{ request('sort') === 'division' ? 'selected' : '' }}>Divisi</option>
            </select>
        </form>
        <a href="{{ route('admin.members.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm shadow-sm">
            <i class="fas fa-plus mr-2"></i> Tambah Anggota
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">ID</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Foto</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Nama Lengkap</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Divisi</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm whitespace-nowrap">{{ $loop->iteration }}</td>
                    <td class="px-3 py-3 md:p-4 whitespace-nowrap">
                        @if($member->photo)
                            <img src="{{ asset('storage/'.$member->photo) }}" class="w-9 h-9 rounded-full object-cover">
                        @else
                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                        @endif
                    </td>
                    <td class="px-3 py-3 md:p-4 font-bold text-slate-800 text-sm">{{ $member->full_name }}</td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm whitespace-nowrap">
                        @if($member->division)
                            {{ $member->division->name }}
                        @else
                            <span class="text-slate-400">Bukan Anggota</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm whitespace-nowrap">
                        <a href="{{ route('admin.members.edit', $member) }}" class="text-blue-500 hover:text-blue-700 inline-block"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.members.destroy', $member) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
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
    </div>
</div>
@endsection
