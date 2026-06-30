@extends('layouts.app')
@section('title', 'Portofolio ' . $member->full_name)
@section('content')
<div class="py-12 bg-slate-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden mb-8 relative" data-aos="fade-up">
            <div class="h-32" style="background-color: {{ $member->division->color_hex ?? '#1D4ED8' }}"></div>
            <div class="px-8 pb-8 flex flex-col sm:flex-row items-center sm:items-end -mt-16 sm:-mt-20 gap-6">
                <div class="w-32 h-32 sm:w-40 sm:h-40 bg-white rounded-full p-2 shadow-lg">
                    @if($member->photo)
                        <img src="{{ asset('storage/'.$member->photo) }}" class="w-full h-full object-cover rounded-full">
                    @else
                        <div class="w-full h-full bg-slate-100 rounded-full flex items-center justify-center text-4xl text-slate-400">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1 text-center sm:text-left mb-2">
                    <h1 class="text-3xl font-bold text-slate-800">{{ $member->full_name }}</h1>
                    <p class="text-lg font-medium" style="color: {{ $member->division->color_hex ?? '#64748B' }}">{{ $member->division->name ?? 'Tim Kreatif' }} {{ $member->role ? '• '.$member->role : '' }}</p>
                </div>
                <div class="flex gap-3 mb-2">
                    @if($member->instagram)
                    <a href="{{ $member->instagram }}" target="_blank" class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center hover:bg-pink-600 hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                    @endif
                    @if($member->linkedin)
                    <a href="{{ $member->linkedin }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    @endif
                    @if($member->github)
                    <a href="{{ $member->github }}" target="_blank" class="w-10 h-10 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center hover:bg-slate-800 hover:text-white transition-colors"><i class="fab fa-github"></i></a>
                    @endif
                    <a href="{{ route('portfolio.pdf', $member->id) }}" class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors" title="Download PDF"><i class="fas fa-file-pdf"></i></a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Riwayat Kegiatan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" data-aos="fade-up" data-aos-delay="100">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2"><i class="fas fa-calendar-check text-blue-600"></i> Riwayat Kegiatan</h3>
                @if($member->events->count() > 0)
                    <div class="space-y-4">
                        @foreach($member->events as $event)
                        <div class="p-4 rounded-xl border border-slate-100 hover:shadow-md transition-shadow">
                            <h4 class="font-bold text-slate-800">{{ $event->title }}</h4>
                            <p class="text-sm text-slate-500 mt-1"><i class="far fa-calendar-alt"></i> {{ $event->event_date->format('d M Y') }}</p>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 italic">Belum ada riwayat kegiatan.</p>
                @endif
            </div>

            <!-- Riwayat Berita / Portofolio -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6" data-aos="fade-up" data-aos-delay="200">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center gap-2"><i class="fas fa-newspaper text-blue-600"></i> Hasil Karya / Berita</h3>
                @if($member->news->count() > 0)
                    <div class="space-y-4">
                        @foreach($member->news as $news)
                        <a href="{{ route('news.show', $news->slug) }}" class="block p-4 rounded-xl border border-slate-100 hover:border-blue-300 hover:shadow-md transition-all group">
                            <h4 class="font-bold text-slate-800 group-hover:text-blue-600 transition-colors">{{ $news->title }}</h4>
                            <p class="text-sm text-slate-500 mt-1"><i class="far fa-calendar-alt"></i> {{ $news->published_date->format('d M Y') }}</p>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-500 italic">Belum ada karya yang dipublikasikan.</p>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
