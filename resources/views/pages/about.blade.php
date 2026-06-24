@extends('layouts.app')
@section('title', 'Tentang Kami - Tim Kreatif PMB UAD')
@section('content')
<div class="py-12 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-bold text-slate-800">Tentang Kami</h1>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
            <p class="mt-4 text-slate-600 text-lg max-w-3xl mx-auto">Kami adalah tim yang berdedikasi untuk memperkenalkan dan mempromosikan Universitas Ahmad Dahlan melalui berbagai media kreatif.</p>
        </div>

        @if($periods->count() > 0)
        <div x-data="{ activePeriod: {{ $periods->first()->id }} }">
            <!-- Period Tabs -->
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                @foreach($periods as $period)
                <button 
                    @click="activePeriod = {{ $period->id }}"
                    :class="activePeriod === {{ $period->id }} ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-6 py-2.5 rounded-full font-semibold transition-all duration-300">
                    Periode {{ $period->name }}
                </button>
                @endforeach
            </div>

            <!-- Members Grid -->
            <div>
                @foreach($periods as $period)
                <div x-show="activePeriod === {{ $period->id }}" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8"
                     style="display: none;">
                    
                    @forelse($period->members as $member)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-xl transition-all hover:-translate-y-2 group">
                        <div class="aspect-w-3 aspect-h-4 bg-slate-200 overflow-hidden relative">
                            @if($member->photo)
                                <img src="{{ asset('storage/'.$member->photo) }}" alt="{{ $member->full_name }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400 bg-slate-100">
                                    <i class="fas fa-user text-5xl"></i>
                                </div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-slate-800 mb-1 line-clamp-1">{{ $member->full_name }}</h3>
                            <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold mt-2" 
                                 style="background-color: {{ $member->division ? $member->division->color_hex . '20' : '#e2e8f0' }}; 
                                        color: {{ $member->division ? $member->division->color_hex : '#64748b' }}">
                                {{ $member->division ? $member->division->name : 'Anggota' }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-slate-600 mb-1">Belum ada data anggota</h3>
                        <p class="text-slate-500 text-sm">Belum ada anggota yang terdaftar pada periode ini.</p>
                    </div>
                    @endforelse

                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="text-center py-12 bg-white rounded-2xl border border-slate-100">
            <i class="fas fa-calendar-times text-5xl text-slate-300 mb-4"></i>
            <h3 class="text-xl font-bold text-slate-700">Belum Ada Periode</h3>
            <p class="text-slate-500 mt-2">Data periode dan anggota belum ditambahkan.</p>
        </div>
        @endif
    </div>
</div>
@endsection
