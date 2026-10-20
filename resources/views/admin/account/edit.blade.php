@extends('layouts.admin')
@section('content')
<div class="max-w-4xl">
    <h1 class="text-2xl font-bold text-slate-800 mb-1">Pengaturan Akun</h1>
    <p class="text-sm text-slate-500 mb-6">Kelola kredensial akun admin dan anggota.</p>

    <!-- Personal Account -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 mb-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h2 class="font-bold text-base text-slate-800">Akun Saya</h2>
                <p class="text-xs text-slate-400">Username & password login dashboard admin</p>
            </div>
        </div>

        <form action="{{ route('admin.account.update') }}" method="POST">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Username</label>
                    <input type="text" name="username" value="{{ $user->username }}" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium" required>
                    @error('username') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Password Saat Ini <span class="text-red-400">*</span></label>
                    <input type="password" name="current_password" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required placeholder="Wajib diisi">
                    @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Password Baru <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="password" name="password" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Kosongkan jika tidak ingin ganti">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ulangi password baru">
                </div>
            </div>
            
            <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-bold text-sm shadow-sm transition-colors">
                <i class="fas fa-save mr-1.5"></i> Simpan Perubahan
            </button>
        </form>
    </div>

    <!-- Member Accounts Management (Admin Only) -->
    @if($memberAccounts !== null)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" x-data="{ editingId: null, editUsername: '', editPassword: '' }">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-users-cog"></i>
                </div>
                <div>
                    <h2 class="font-bold text-base text-slate-800">Akun Dashboard Anggota</h2>
                    <p class="text-xs text-slate-400">Kelola username & password login anggota</p>
                </div>
            </div>
            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">{{ $memberAccounts->count() }} Akun</span>
        </div>

        @if($memberAccounts->isEmpty())
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-user-slash text-2xl mb-2"></i>
                <p class="text-sm">Belum ada akun anggota terdaftar.</p>
            </div>
        @else
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="text-left px-4 py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="text-left px-4 py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Username</th>
                            <th class="text-left px-4 py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Password</th>
                            <th class="text-right px-4 py-2.5 text-xs font-bold text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($memberAccounts as $idx => $member)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-slate-400 font-mono text-xs">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3">
                                <template x-if="editingId === {{ $member->id }}">
                                    <input type="text" x-model="editUsername" class="w-full px-2.5 py-1.5 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 font-medium">
                                </template>
                                <template x-if="editingId !== {{ $member->id }}">
                                    <span class="font-semibold text-slate-800">{{ $member->username }}</span>
                                </template>
                            </td>
                            <td class="px-4 py-3">
                                <template x-if="editingId === {{ $member->id }}">
                                    <input type="password" x-model="editPassword" placeholder="Kosongkan jika tidak ganti" class="w-full px-2.5 py-1.5 border border-blue-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500">
                                </template>
                                <template x-if="editingId !== {{ $member->id }}">
                                    <span class="text-slate-400 text-xs font-mono">••••••••</span>
                                </template>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <template x-if="editingId === {{ $member->id }}">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <form method="POST" action="{{ route('admin.account.update-member', $member->id) }}" x-ref="form{{ $member->id }}">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="username" :value="editUsername">
                                            <input type="hidden" name="password" :value="editPassword">
                                            <button type="submit" class="px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">
                                                <i class="fas fa-check mr-0.5"></i> Simpan
                                            </button>
                                        </form>
                                        <button @click="editingId = null" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-colors">Batal</button>
                                    </div>
                                </template>
                                <template x-if="editingId !== {{ $member->id }}">
                                    <button @click="editingId = {{ $member->id }}; editUsername = '{{ $member->username }}'; editPassword = ''" class="px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold hover:bg-amber-100 transition-colors">
                                        <i class="fas fa-edit mr-0.5"></i> Edit
                                    </button>
                                </template>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @endif
</div>
@endsection
