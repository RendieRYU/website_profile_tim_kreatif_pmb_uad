@extends('layouts.admin')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Statistik & Metrik</h1>
    <a href="{{ route('admin.metrics.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
        <i class="fas fa-plus mr-2"></i> Tambah Data
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden mb-6">
    <div class="p-4 border-b border-slate-100 flex flex-wrap gap-2">
        <a href="{{ route('admin.metrics.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == '' ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">Semua</a>
        <a href="{{ route('admin.metrics.index', ['type' => 'registrant']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == 'registrant' ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-50' }}">Pendaftar UAD</a>
        <a href="{{ route('admin.metrics.index', ['type' => 'instagram']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == 'instagram' ? 'bg-pink-50 text-pink-700' : 'text-slate-600 hover:bg-slate-50' }}">IG Followers</a>
        <a href="{{ route('admin.metrics.index', ['type' => 'tiktok']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == 'tiktok' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-50' }}">TikTok Views</a>
        <a href="{{ route('admin.metrics.index', ['type' => 'instagram_live']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == 'instagram_live' ? 'bg-pink-50 text-pink-700' : 'text-slate-600 hover:bg-slate-50' }}">Live IG</a>
        <a href="{{ route('admin.metrics.index', ['type' => 'tiktok_live']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('type') == 'tiktok_live' ? 'bg-slate-800 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Live TikTok</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-slate-500 text-sm">
                <tr>
                    <th class="p-4 font-medium">Tanggal</th>
                    <th class="p-4 font-medium">Jenis Metrik</th>
                    <th class="p-4 font-medium">Nilai / Jumlah</th>
                    <th class="p-4 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($metrics as $metric)
                <tr class="hover:bg-slate-50 transition-colors">
                    <td class="p-4 text-slate-800">{{ $metric->date->format('d M Y') }}</td>
                    <td class="p-4 text-slate-600">
                        @if($metric->type == 'registrant')
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Pendaftar UAD</span>
                        @elseif($metric->type == 'instagram')
                            <span class="bg-pink-100 text-pink-800 text-xs font-medium px-2.5 py-0.5 rounded">Instagram</span>
                        @elseif($metric->type == 'instagram_live')
                            <span class="bg-pink-100 text-pink-800 text-xs font-medium px-2.5 py-0.5 rounded"><i class="fas fa-video mr-1"></i> Live IG</span>
                        @elseif($metric->type == 'tiktok_live')
                            <span class="bg-gray-200 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded"><i class="fas fa-video mr-1"></i> Live TikTok</span>
                        @else
                            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">TikTok</span>
                        @endif
                    </td>
                    <td class="p-4 font-semibold text-slate-800">{{ number_format($metric->value, 0, ',', '.') }}</td>
                    <td class="p-4 text-right">
                        <form action="{{ route('admin.metrics.destroy', $metric->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 p-2"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">Belum ada data statistik.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($metrics->hasPages())
    <div class="p-4 border-t border-slate-100">
        {{ $metrics->appends(['type' => request('type')])->links() }}
    </div>
    @endif
</div>
@endsection
