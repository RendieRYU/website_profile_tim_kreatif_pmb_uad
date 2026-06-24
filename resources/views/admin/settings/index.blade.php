@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Pengaturan Website</h1>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        
        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Identitas Website</h2>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Logo Website (Navbar)</label>
                @if(isset($settings['site_logo']) && $settings['site_logo'])
                    <img src="{{ asset('storage/'.$settings['site_logo']) }}" class="h-16 object-contain mb-2 bg-slate-100 p-2 rounded">
                @endif
                <input type="file" name="site_logo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Foto Bersama (Hero Dashboard)</h2>
            @if(isset($settings['team_photo']) && $settings['team_photo'])
                <img src="{{ asset('storage/'.$settings['team_photo']) }}" class="w-full max-w-lg rounded-lg mb-4">
            @endif
            <input type="file" name="team_photo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Statistik Pendaftar</h2>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah Pendaftar UAD</label>
                <input type="number" name="registrant_count" value="{{ $settings['registrant_count'] ?? 0 }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Statistik Social Media</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Tampilkan Statistik</label>
                <select name="social_media_display" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="both" {{ ($settings['social_media_display'] ?? '') == 'both' ? 'selected' : '' }}>Instagram & TikTok</option>
                    <option value="ig" {{ ($settings['social_media_display'] ?? '') == 'ig' ? 'selected' : '' }}>Hanya Instagram</option>
                    <option value="tiktok" {{ ($settings['social_media_display'] ?? '') == 'tiktok' ? 'selected' : '' }}>Hanya TikTok</option>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="p-4 border border-slate-100 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-700 mb-3"><i class="fab fa-instagram text-pink-500 mr-2"></i> Instagram</h3>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Instagram Followers</label>
                    <input type="text" name="ig_followers" value="{{ $settings['ig_followers'] ?? '' }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 mb-3">
                    
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar Instagram</label>
                    @if(isset($settings['instagram_image']) && $settings['instagram_image'])
                        <img src="{{ asset('storage/'.$settings['instagram_image']) }}" class="w-32 rounded-lg mb-2">
                    @endif
                    <input type="file" name="instagram_image" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-3">

                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Performa Instagram</label>
                    <textarea name="instagram_description" rows="3" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $settings['instagram_description'] ?? '' }}</textarea>
                </div>
                <div class="p-4 border border-slate-100 rounded-xl bg-slate-50">
                    <h3 class="font-bold text-slate-700 mb-3"><i class="fab fa-tiktok text-black mr-2"></i> TikTok</h3>
                    <label class="block text-sm font-medium text-slate-700 mb-1">TikTok Views</label>
                    <input type="text" name="tiktok_views" value="{{ $settings['tiktok_views'] ?? '' }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 mb-3">
                    
                    <label class="block text-sm font-medium text-slate-700 mb-1">Gambar TikTok</label>
                    @if(isset($settings['tiktok_image']) && $settings['tiktok_image'])
                        <img src="{{ asset('storage/'.$settings['tiktok_image']) }}" class="w-32 rounded-lg mb-2">
                    @endif
                    <input type="file" name="tiktok_image" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 mb-3">

                    <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Performa TikTok</label>
                    <textarea name="tiktok_description" rows="3" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ $settings['tiktok_description'] ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-xl font-bold text-slate-800 mb-4 border-b pb-2">Link Social Media</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link Instagram</label>
                    <input type="url" name="instagram_link" value="{{ $settings['instagram_link'] ?? '' }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link TikTok</label>
                    <input type="url" name="tiktok_link" value="{{ $settings['tiktok_link'] ?? '' }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
