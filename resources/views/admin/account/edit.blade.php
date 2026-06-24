@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Pengaturan Akun</h1>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.account.update') }}" method="POST">
        @csrf @method('PUT')
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" value="{{ $user->name }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Username</label>
            <input type="text" name="username" value="{{ $user->username }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
        </div>
        
        <hr class="my-6 border-slate-200">
        
        <h2 class="text-lg font-bold text-slate-800 mb-4">Ganti Password (Opsional)</h2>
        <p class="text-sm text-slate-500 mb-4">Kosongkan jika tidak ingin mengubah password.</p>
        
        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Password Baru</label>
            <input type="password" name="password" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password Baru</label>
            <input type="password" name="password_confirmation" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
        </div>
        
        <div>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">Simpan Perubahan</button>
        </div>
    </form>
</div>
@endsection
