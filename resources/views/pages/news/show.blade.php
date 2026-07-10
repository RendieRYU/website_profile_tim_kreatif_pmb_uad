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



    </div>
</div>
@endsection
