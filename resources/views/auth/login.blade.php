@extends('layouts.app')

@section('title', 'Login Admin - Tim Kreatif PMB UAD')

@section('content')
<div class="flex justify-center items-center py-20 px-4 min-h-[70vh]">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden animate-fade-in border border-slate-100">
        <div class="bg-blue-600 p-8 text-center">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg text-blue-600 text-2xl font-bold">
                <i class="fas fa-lock"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">Login Admin</h2>
            <p class="text-blue-100 mt-1">Tim Kreatif PMB UAD</p>
        </div>
        <div class="p-8">
            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-2">Username</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-user"></i>
                        </div>
                        <input type="text" name="username" id="username" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors @error('username') border-red-500 @enderror" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    </div>
                    @error('username')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-key"></i>
                        </div>
                        <input type="password" name="password" id="password" class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 transition-colors @error('password') border-red-500 @enderror" placeholder="••••••••" required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 font-medium transition-all hover:scale-[1.02]">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
