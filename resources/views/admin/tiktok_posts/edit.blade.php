@extends('layouts.admin')
@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Edit Laporan Postingan TikTok</h1>
    <p class="text-slate-500 text-sm mt-1">Perbarui metrik analitik postingan TikTok.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.tiktok-posts.update', $tiktokPost->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Section 1: Utama -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500"></i> Informasi Utama</h3>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Posting</label>
                    <input type="date" name="tanggal_posting" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('tanggal_posting') border-red-500 @enderror" value="{{ old('tanggal_posting', $tiktokPost->tanggal_posting->format('Y-m-d')) }}" required>
                    @error('tanggal_posting') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link Konten (TikTok URL)</label>
                    <input type="url" name="link_konten" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('link_konten') border-red-500 @enderror" value="{{ old('link_konten', $tiktokPost->link_konten) }}" placeholder="https://www.tiktok.com/@...">
                    @error('link_konten') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Konten</label>
                    <select name="jenis_konten" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('jenis_konten') border-red-500 @enderror" required>
                        <option value="VIDEO" {{ old('jenis_konten', $tiktokPost->jenis_konten) === 'VIDEO' ? 'selected' : '' }}>VIDEO</option>
                        <option value="STORY" {{ old('jenis_konten', $tiktokPost->jenis_konten) === 'STORY' ? 'selected' : '' }}>STORY</option>
                        <option value="LIVE" {{ old('jenis_konten', $tiktokPost->jenis_konten) === 'LIVE' ? 'selected' : '' }}>LIVE</option>
                        <option value="OTHERS" {{ old('jenis_konten', $tiktokPost->jenis_konten) === 'OTHERS' ? 'selected' : '' }}>OTHERS</option>
                    </select>
                    @error('jenis_konten') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pillar Konten</label>
                    <input type="text" name="pillar" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('pillar') border-red-500 @enderror" value="{{ old('pillar', $tiktokPost->pillar) }}" placeholder="Contoh: INFORMATION, ENTERTAINMENT">
                    @error('pillar') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 2: Jangkauan (Reach/Impression) -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-chart-bar text-slate-800"></i> Views & Jangkauan</h3>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reach / Total Views</label>
                    <input type="number" name="reach" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('reach') border-red-500 @enderror" value="{{ old('reach', $tiktokPost->reach) }}" min="0">
                    @error('reach') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: For You Page</label>
                        <input type="number" name="impression_home" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_home', $tiktokPost->impression_home) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Profile</label>
                        <input type="number" name="impression_profile" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_profile', $tiktokPost->impression_profile) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Search</label>
                        <input type="number" name="impression_explore" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_explore', $tiktokPost->impression_explore) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Hashtag/Sound</label>
                        <input type="number" name="impression_hashtag" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_hashtag', $tiktokPost->impression_hashtag) }}" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Impression: Other</label>
                    <input type="number" name="impression_other" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_other', $tiktokPost->impression_other) }}" min="0">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Section 3: Rasio Interaksi Akun -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-users-cog text-purple-500"></i> Rasio Penonton</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Followers (%)</label>
                        <input type="number" step="0.01" name="interaction_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('interaction_followers', $tiktokPost->interaction_followers) }}" min="0" max="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Non-Followers (%)</label>
                        <input type="number" step="0.01" name="interaction_non_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('interaction_non_followers', $tiktokPost->interaction_non_followers) }}" min="0" max="100">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Followers Baru (New Followers)</label>
                    <input type="number" name="new_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('new_followers') border-red-500 @enderror" value="{{ old('new_followers', $tiktokPost->new_followers) }}" min="0">
                    @error('new_followers') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 4: Metrik Interaksi -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-thumbs-up text-emerald-500"></i> Interaksi (Engagement)</h3>
                
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Likes</label>
                        <input type="number" name="likes" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('likes', $tiktokPost->likes) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Comments</label>
                        <input type="number" name="comments" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('comments', $tiktokPost->comments) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Shares</label>
                        <input type="number" name="shares" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('shares', $tiktokPost->shares) }}" min="0">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Saves (Favorit)</label>
                        <input type="number" name="saves" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('saves', $tiktokPost->saves) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Reposts</label>
                        <input type="number" name="reposts" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('reposts', $tiktokPost->reposts) }}" min="0">
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
            <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold text-sm transition-colors shadow-sm">
                <i class="fas fa-save mr-2"></i> Perbarui Laporan
            </button>
            <a href="{{ route('admin.tiktok-posts.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 rounded-lg hover:bg-slate-200 font-semibold text-sm transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
