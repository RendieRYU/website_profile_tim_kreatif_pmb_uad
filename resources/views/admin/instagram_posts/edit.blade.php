@extends('layouts.admin')
@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-slate-800">Edit Laporan Postingan Instagram</h1>
    <p class="text-slate-500 text-sm mt-1">Ubah atau sesuaikan metrik analitik postingan Instagram.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.instagram-posts.update', $instagramPost->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Section 1: Utama -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-info-circle text-blue-500"></i> Informasi Utama</h3>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal Posting</label>
                    <input type="date" name="tanggal_posting" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('tanggal_posting') border-red-500 @enderror" value="{{ old('tanggal_posting', $instagramPost->tanggal_posting->format('Y-m-d')) }}" required>
                    @error('tanggal_posting') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Link Konten (Instagram URL)</label>
                    <input type="url" name="link_konten" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('link_konten') border-red-500 @enderror" value="{{ old('link_konten', $instagramPost->link_konten) }}" placeholder="https://www.instagram.com/p/...">
                    @error('link_konten') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Jenis Konten</label>
                    <select name="jenis_konten" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('jenis_konten') border-red-500 @enderror" required>
                        <option value="FEED" {{ old('jenis_konten', $instagramPost->jenis_konten) === 'FEED' ? 'selected' : '' }}>FEED</option>
                        <option value="REELS" {{ old('jenis_konten', $instagramPost->jenis_konten) === 'REELS' ? 'selected' : '' }}>REELS</option>
                        <option value="STORY" {{ old('jenis_konten', $instagramPost->jenis_konten) === 'STORY' ? 'selected' : '' }}>STORY</option>
                        <option value="OTHERS" {{ old('jenis_konten', $instagramPost->jenis_konten) === 'OTHERS' ? 'selected' : '' }}>OTHERS</option>
                    </select>
                    @error('jenis_konten') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pillar Konten</label>
                    <input type="text" name="pillar" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('pillar') border-red-500 @enderror" value="{{ old('pillar', $instagramPost->pillar) }}" placeholder="Contoh: INFORMATION, ENTERTAINMENT">
                    @error('pillar') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 2: Jangkauan (Reach/Impression) -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-chart-bar text-pink-500"></i> Jangkauan & Impresi</h3>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reach (Jangkauan)</label>
                    <input type="number" name="reach" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('reach') border-red-500 @enderror" value="{{ old('reach', $instagramPost->reach) }}" min="0">
                    @error('reach') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Home</label>
                        <input type="number" name="impression_home" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_home', $instagramPost->impression_home) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Profile</label>
                        <input type="number" name="impression_profile" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_profile', $instagramPost->impression_profile) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Explore</label>
                        <input type="number" name="impression_explore" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_explore', $instagramPost->impression_explore) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Impression: Hashtag</label>
                        <input type="number" name="impression_hashtag" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_hashtag', $instagramPost->impression_hashtag) }}" min="0">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Impression: Other</label>
                    <input type="number" name="impression_other" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('impression_other', $instagramPost->impression_other) }}" min="0">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Section 3: Rasio Interaksi Akun -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-users-cog text-purple-500"></i> Rasio Pengikut</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Followers (%)</label>
                        <input type="number" step="0.01" name="interaction_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('interaction_followers', $instagramPost->interaction_followers) }}" min="0" max="100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Non-Followers (%)</label>
                        <input type="number" step="0.01" name="interaction_non_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('interaction_non_followers', $instagramPost->interaction_non_followers) }}" min="0" max="100">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Followers Baru (New Followers)</label>
                    <input type="number" name="new_followers" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('new_followers') border-red-500 @enderror" value="{{ old('new_followers', $instagramPost->new_followers) }}" min="0">
                    @error('new_followers') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Section 4: Interaksi (Engagement) -->
            <div class="space-y-4">
                <h3 class="text-lg font-bold text-slate-800 pb-2 border-b border-slate-100 flex items-center gap-2"><i class="fas fa-heart text-red-500"></i> Interaksi Postingan</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Likes</label>
                        <input type="number" name="likes" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('likes', $instagramPost->likes) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Comments</label>
                        <input type="number" name="comments" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('comments', $instagramPost->comments) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Shares</label>
                        <input type="number" name="shares" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('shares', $instagramPost->shares) }}" min="0">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Saves</label>
                        <input type="number" name="saves" class="w-full px-3 py-1.5 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('saves', $instagramPost->saves) }}" min="0">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Reposts</label>
                    <input type="number" name="reposts" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500" value="{{ old('reposts', $instagramPost->reposts) }}" min="0">
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 border-t border-slate-100 pt-6">
            <a href="{{ route('admin.instagram-posts.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-700 font-semibold transition-all">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold transition-all shadow-sm">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
