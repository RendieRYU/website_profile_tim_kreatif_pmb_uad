@extends('layouts.admin')

@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Kategori Kegiatan</h1>
    <a href="{{ route('admin.categories.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-semibold text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah Kategori
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100 text-slate-500 text-sm uppercase tracking-wider">
                    <th class="px-3 py-3 md:p-4 font-semibold text-sm">Nama Kategori</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-sm">Warna Label</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($categories as $category)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200">
                            <span class="w-2 h-2 rounded-full mr-1.5" style="background-color: {{ $category->color }}"></span>
                            {{ $category->name }}
                        </span>
                    <td class="px-3 py-3 md:p-4">
                        <div class="flex items-center text-slate-600">
                            <div class="w-6 h-6 rounded border border-slate-200 mr-2 shadow-sm" style="background-color: {{ $category->color }}"></div>
                            <span class="text-sm font-mono">{{ $category->color }}</span>
                        </div>
                    </td>
                    <td class="px-3 py-3 md:p-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kategori ini?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="p-8 text-center text-slate-500">
                        <i class="fas fa-tags text-4xl mb-3 text-slate-300"></i>
                        <p>Belum ada kategori kegiatan yang ditambahkan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
