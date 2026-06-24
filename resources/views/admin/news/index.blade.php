@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Manajemen Berita / Karya</h1>
    <a href="{{ route('admin.news.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"><i class="fas fa-plus mr-2"></i> Tambah Berita</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="p-4 font-medium text-slate-600">ID</th>
                <th class="p-4 font-medium text-slate-600">Judul</th>
                <th class="p-4 font-medium text-slate-600">Tanggal Publish</th>
                <th class="p-4 font-medium text-slate-600">Kreator</th>
                <th class="p-4 font-medium text-slate-600 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($news as $item)
            <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                <td class="p-4 text-slate-600">{{ $item->id }}</td>
                <td class="p-4 font-bold text-slate-800">{{ $item->title }}</td>
                <td class="p-4 text-slate-600">{{ $item->published_date->format('d M Y') }}</td>
                <td class="p-4 text-slate-600">
                    <span class="px-2 py-1 bg-slate-100 rounded-lg text-xs">{{ $item->members->count() }} Anggota</span>
                </td>
                <td class="p-4 text-right space-x-2">
                    <a href="{{ route('news.show', $item->slug) }}" target="_blank" class="text-green-500 hover:text-green-700" title="Lihat"><i class="fas fa-eye"></i></a>
                    <a href="{{ route('admin.news.edit', $item) }}" class="text-blue-500 hover:text-blue-700" title="Edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus?')">
                        @csrf @method('DELETE')
                        <button class="text-red-500 hover:text-red-700" title="Hapus"><i class="fas fa-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="p-8 text-center text-slate-500">Belum ada data berita.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="p-4 border-t border-slate-100">
        {{ $news->links() }}
    </div>
</div>
@endsection
