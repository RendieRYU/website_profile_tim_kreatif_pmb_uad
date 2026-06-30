@extends('layouts.app')
@section('title', $news->title . ' - Tim Kreatif PMB UAD')
@section('content')
<div class="py-12 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            @if($news->banner_image)
                <img src="{{ asset('storage/'.$news->banner_image) }}" alt="{{ $news->title }}" class="w-full h-auto rounded-2xl shadow-md mb-8">
            @endif
            
            <h1 class="text-3xl md:text-5xl font-bold text-slate-900 mb-4">{{ $news->title }}</h1>
            <div class="flex items-center text-slate-500 gap-4 text-sm font-medium border-b border-slate-100 pb-6">
                <span><i class="far fa-calendar-alt mr-1 text-blue-500"></i> {{ $news->published_date->format('d F Y') }}</span>
                <span><i class="fas fa-users mr-1 text-blue-500"></i> Oleh Tim Kreatif</span>
            </div>
        </div>

        <div class="prose prose-lg prose-blue max-w-none text-slate-700">
            {!! $news->content !!}
        </div>

        @if($news->members->count() > 0)
        <div class="mt-12 pt-8 border-t border-slate-100">
            <h3 class="text-xl font-bold text-slate-800 mb-6">Kreator Terlibat</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($news->members as $member)
                <a href="{{ route('portfolio.show', ['q' => $member->full_name]) }}" class="flex items-center gap-4 p-4 bg-slate-50 rounded-xl hover:bg-blue-50 transition-colors border border-slate-100">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                        {{ substr($member->full_name, 0, 1) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-800">{{ $member->full_name }}</div>
                        <div class="text-xs text-slate-500">{{ $member->division->name ?? 'Tim Kreatif' }}</div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
