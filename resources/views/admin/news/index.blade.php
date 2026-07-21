@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Berita</h1>
    <a href="{{ route('admin.news.create') }}" class="w-full sm:w-auto text-center px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-sm font-semibold text-sm">
        <i class="fas fa-plus mr-2"></i> Tambah Berita
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse whitespace-nowrap">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">ID</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Judul</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm">Tanggal Publish</th>
                    <th class="px-3 py-3 md:p-4 font-semibold text-slate-600 text-sm text-right">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($news as $item)
                <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">{{ $news->firstItem() + $loop->index }}</td>
                    <td class="px-3 py-3 md:p-4 font-bold text-slate-800 text-sm">{{ $item->title }}</td>
                    <td class="px-3 py-3 md:p-4 text-slate-600 text-sm">{{ $item->published_date->format('d M Y') }}</td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm">
                        <a href="{{ route('news.show', $item->slug) }}" target="_blank" class="text-green-500 hover:text-green-700 inline-block" title="Lihat"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('admin.news.edit', $item) }}" class="text-blue-500 hover:text-blue-700 inline-block" title="Edit"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">Belum ada data berita.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-slate-100">
        {{ $news->links() }}
    </div>
</div>
@endsection
