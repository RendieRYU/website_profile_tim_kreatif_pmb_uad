@extends('layouts.admin')
@section('content')
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Laporan Postingan Instagram</h1>
        <p class="text-slate-500 text-sm mt-1">Kelola data metrik dan interaksi postingan Instagram secara bulanan (Periode {{ $activePeriod->name }})</p>
    </div>
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full sm:w-auto">
        <a href="{{ route('admin.instagram-posts.export', ['month' => request('month')]) }}" class="w-full sm:w-auto px-4 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
            <i class="fas fa-file-excel"></i> Ekspor Excel
        </a>
        <a href="{{ route('admin.instagram-posts.create') }}" class="w-full sm:w-auto px-4 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 text-sm font-semibold shadow-sm">
            <i class="fas fa-plus"></i> Tambah Postingan
        </a>
    </div>
</div>

<!-- Filter Box -->
<div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <form action="{{ route('admin.instagram-posts.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        <label for="month" class="text-sm font-medium text-slate-600">Filter Bulan:</label>
        <select name="month" id="month" onchange="this.form.submit()" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 focus:outline-none focus:border-blue-500 focus:bg-white transition-all">
            <option value="">Semua Bulan</option>
            @foreach($allMonths as $value => $label)
                <option value="{{ $value }}" {{ request('month') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @if(request('month'))
            <a href="{{ route('admin.instagram-posts.index') }}" class="text-sm text-red-500 hover:text-red-700 font-medium"><i class="fas fa-times mr-1"></i> Bersihkan</a>
        @endif
    </form>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
    <div class="overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs font-semibold uppercase tracking-wider">
                <tr>
                    <th class="px-3 py-3 md:p-4">No</th>
                    <th class="px-3 py-3 md:p-4">Tanggal</th>
                    <th class="px-3 py-3 md:p-4">Jenis</th>
                    <th class="px-3 py-3 md:p-4">Pillar</th>
                    <th class="px-3 py-3 md:p-4 text-center">Likes</th>
                    <th class="px-3 py-3 md:p-4 text-center">Comments</th>
                    <th class="px-3 py-3 md:p-4 text-center">Shares</th>
                    <th class="px-3 py-3 md:p-4 text-center">Saves</th>
                    <th class="px-3 py-3 md:p-4 text-center">Reposts</th>
                    <th class="px-3 py-3 md:p-4 text-center bg-slate-50/50 font-bold text-slate-700">Total Eng.</th>
                    <th class="px-3 py-3 md:p-4 text-center">Reach</th>
                    <th class="px-3 py-3 md:p-4 text-center">New Followers</th>
                    <th class="px-3 py-3 md:p-4 text-center text-blue-600 font-semibold">ER by Post</th>
                    <th class="px-3 py-3 md:p-4 text-center text-purple-600 font-semibold">ER by Reach</th>
                    <th class="px-3 py-3 md:p-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($posts as $index => $post)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="px-3 py-3 md:p-4 text-slate-500">{{ $posts->firstItem() + $index }}</td>
                    <td class="px-3 py-3 md:p-4 text-slate-800 font-medium">
                        {{ $post->tanggal_posting->translatedFormat('d M Y') }}
                    </td>
                    <td class="px-3 py-3 md:p-4">
                        @if($post->jenis_konten === 'REELS')
                            <span class="bg-pink-100 text-pink-800 text-xs font-semibold px-2 py-0.5 rounded-full"><i class="fab fa-instagram mr-1"></i> REELS</span>
                        @elseif($post->jenis_konten === 'FEED')
                            <span class="bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full"><i class="far fa-image mr-1"></i> FEED</span>
                        @elseif($post->jenis_konten === 'STORY')
                            <span class="bg-amber-100 text-amber-800 text-xs font-semibold px-2 py-0.5 rounded-full"><i class="far fa-play-circle mr-1"></i> STORY</span>
                        @else
                            <span class="bg-slate-100 text-slate-800 text-xs font-semibold px-2 py-0.5 rounded-full">OTHERS</span>
                        @endif
                    </td>
                    <td class="px-3 py-3 md:p-4 text-slate-600">{{ $post->pillar ?? '-' }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->likes, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->comments, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->shares, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->saves, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->reposts, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center font-bold bg-slate-50/30 text-slate-800">{{ number_format($post->total_engagement, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->reach, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center text-slate-700">{{ number_format($post->new_followers, 0, ',', '.') }}</td>
                    <td class="px-3 py-3 md:p-4 text-center font-semibold text-blue-600">{{ $post->er_by_post }}%</td>
                    <td class="px-3 py-3 md:p-4 text-center font-semibold text-purple-600">{{ $post->er_by_reach }}%</td>
                    <td class="px-3 py-3 md:p-4 text-right space-x-1">
                        @if($post->link_konten)
                            <a href="{{ $post->link_konten }}" target="_blank" class="text-slate-400 hover:text-slate-600 p-1" title="Buka Link Konten"><i class="fas fa-external-link-alt"></i></a>
                        @endif
                        <a href="{{ route('admin.instagram-posts.edit', $post->id) }}" class="text-blue-500 hover:text-blue-700 p-1" title="Edit Data"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.instagram-posts.destroy', $post->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan postingan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-1" title="Hapus"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="15" class="p-8 text-center text-slate-500">Belum ada data postingan Instagram pada periode/bulan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($posts->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50">
        {{ $posts->links() }}
    </div>
    @endif
</div>
@endsection
