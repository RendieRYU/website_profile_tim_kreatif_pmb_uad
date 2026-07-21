@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Karya / Portofolio</h1>
    <a href="{{ route('admin.works.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-semibold text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah Karya
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Judul Karya</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Jenis</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Tanggal</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Pembuat</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-center w-32">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($works as $work)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-800 text-sm font-medium">
                        {{ $work->title }}
                        @if($work->link)
                            <a href="{{ $work->link }}" target="_blank" class="ml-2 text-blue-600 hover:underline text-sm"><i class="fas fa-external-link-alt"></i></a>
                        @endif
                    </td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">
                        <span class="px-2 py-1 bg-slate-100 text-slate-600 text-xs rounded border border-slate-200">{{ $work->work_type }}</span>
                    </td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">{{ $work->work_date->format('d M Y') }}</td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">
                        @foreach($work->members as $member)
                            <span class="inline-block px-2 py-1 bg-blue-50 text-blue-600 text-xs rounded border border-blue-100 mb-1 mr-1">{{ $member->full_name }}</span>
                        @endforeach
                    </td>
                    <td class="px-3 py-3 md:p-4 text-center text-sm">
                        <div class="flex justify-center gap-2">
                            <a href="{{ route('admin.works.edit', $work) }}" class="w-8 h-8 rounded bg-yellow-50 text-yellow-600 flex items-center justify-center hover:bg-yellow-500 hover:text-white transition-colors"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.works.destroy', $work) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus karya ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-500 hover:text-white transition-colors"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data karya/portofolio.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">
        {{ $works->links() }}
    </div>
</div>
@endsection
