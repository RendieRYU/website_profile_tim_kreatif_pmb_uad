@extends('layouts.app')

@section('content')

<!-- Hero Section -->
<section class="relative bg-blue-900 text-white py-20 overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-blue-400 via-blue-900 to-slate-900"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold tracking-tight mb-6">Tim Kreatif PMB UAD</h1>
        <p class="text-xl text-blue-200 mb-12 max-w-2xl mx-auto">Kami berkreasi, menginspirasi, dan menjangkau lebih luas untuk Penerimaan Mahasiswa Baru Universitas Ahmad Dahlan.</p>
        
        @if(isset($settings['team_photo']) && $settings['team_photo'])
        <div class="max-w-5xl mx-auto bg-white p-3 rounded-3xl shadow-2xl transform hover:scale-[1.01] transition-transform duration-500">
            <div class="rounded-2xl overflow-hidden border border-slate-100 relative">
                <img src="{{ asset('storage/'.$settings['team_photo']) }}" alt="Foto Bersama Tim Kreatif" class="w-full h-auto object-cover max-h-[500px]">
                <div class="absolute bottom-4 left-4 bg-white/90 backdrop-blur text-blue-900 font-bold px-4 py-2 rounded-lg shadow-sm">
                    <i class="fas fa-users mr-2"></i> Keluarga Besar Tim Kreatif
                </div>
            </div>
        </div>
        @endif
        
        <div class="mt-12">
            <a href="#kegiatan" class="px-8 py-3 bg-white text-blue-900 font-bold rounded-full hover:bg-blue-50 transition-all hover:shadow-lg inline-block">Jelajahi Kegiatan</a>
        </div>
    </div>
</section>

<!-- Pencapaian & Statistik Section -->
<section class="py-16 bg-slate-50" id="pencapaian">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Statistik & Pencapaian</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pendaftar UAD -->
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm text-center flex flex-col justify-center items-center">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-6 text-3xl">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-5xl font-extrabold text-slate-800 mb-2">{{ $settings['registrant_count'] ?? 0 }}</h3>
                <p class="text-slate-500 font-bold text-lg">Pendaftar UAD</p>
                <p class="text-sm text-slate-400 mt-2">Tahun berjalan</p>
            </div>

            <!-- Recap Kegiatan Divisi -->
            <div class="md:col-span-2 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center"><i class="fas fa-chart-pie text-blue-500 mr-3"></i> Rekap Kegiatan per Divisi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($divisions as $div)
                    @php
                        $count = $divisionEventCounts->get($div->id, 0);
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md transition-shadow bg-slate-50">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" style="background-color: {{ $div->color_hex }}"></div>
                                <span class="font-bold text-slate-700">{{ $div->name }}</span>
                            </div>
                        </div>
                        <div class="flex items-end gap-2 mt-3">
                            <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $count }}</span>
                            <span class="text-sm text-slate-500 font-medium mb-1">Kegiatan</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Performance -->
<section class="py-16 bg-white border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'instagram' }">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-800">Performa Media Sosial</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
        </div>

        <!-- Tabs -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex bg-slate-100 rounded-full p-1 shadow-inner">
                <button @click="activeTab = 'instagram'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'instagram', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'instagram'}" class="px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fab fa-instagram mr-2 text-lg" :class="{'text-pink-500': activeTab === 'instagram'}"></i> Instagram
                </button>
                <button @click="activeTab = 'tiktok'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'tiktok', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'tiktok'}" class="px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fab fa-tiktok mr-2 text-lg" :class="{'text-black': activeTab === 'tiktok'}"></i> TikTok
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col md:flex-row min-h-[400px]">
            
            <!-- Instagram View -->
            <div x-show="activeTab === 'instagram'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                @if(isset($settings['instagram_image']) && $settings['instagram_image'])
                <div class="md:w-5/12 bg-slate-100 relative overflow-hidden">
                    <img src="{{ asset('storage/'.$settings['instagram_image']) }}" alt="Instagram Image" class="absolute inset-0 w-full h-full object-cover">
                </div>
                @else
                <div class="md:w-5/12 bg-gradient-to-br from-pink-500 via-purple-500 to-orange-400 p-8 flex items-center justify-center relative overflow-hidden">
                    <i class="fab fa-instagram text-[200px] md:text-[300px] text-white opacity-20 absolute -right-10 -bottom-10"></i>
                    <div class="text-center relative z-10">
                        <div class="w-24 h-24 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg border border-white/30">
                            <i class="fab fa-instagram text-4xl text-white"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-2">Instagram</h3>
                        <p class="text-pink-100 font-medium tracking-wide">PMB UAD OFFICIAL</p>
                    </div>
                </div>
                @endif
                <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4">Performa Instagram</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        {{ $settings['instagram_description'] ?? 'Platform utama untuk membagikan info pendaftaran, dokumentasi acara, dan materi visual menarik seputar kehidupan kampus UAD.' }}
                    </p>
                    
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center mb-4">
                            <i class="fas fa-chart-line text-blue-500 mr-2"></i> Statistik Utama
                        </h4>
                        <div class="inline-flex items-center bg-slate-50 px-6 py-3 rounded-xl border border-slate-100">
                            <div class="mr-4 text-slate-400 text-2xl"><i class="fas fa-users"></i></div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Total Followers</p>
                                <p class="text-2xl font-extrabold text-slate-800">{{ $settings['ig_followers'] ?? '0' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold text-slate-800 flex items-center mb-4">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Fokus Konten
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">Update Informasi PMB & Jadwal Pendaftaran</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">Dokumentasi Event & Kegiatan Kampus</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">Konten Interaktif (QnA, Polling, Live)</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- TikTok View -->
            <div x-show="activeTab === 'tiktok'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                @if(isset($settings['tiktok_image']) && $settings['tiktok_image'])
                <div class="md:w-5/12 bg-slate-100 relative overflow-hidden">
                    <img src="{{ asset('storage/'.$settings['tiktok_image']) }}" alt="TikTok Image" class="absolute inset-0 w-full h-full object-cover">
                </div>
                @else
                <div class="md:w-5/12 bg-gradient-to-br from-slate-800 to-black p-8 flex items-center justify-center relative overflow-hidden">
                    <i class="fab fa-tiktok text-[200px] md:text-[300px] text-white opacity-10 absolute -left-10 -bottom-10"></i>
                    <div class="text-center relative z-10">
                        <div class="w-24 h-24 bg-white/10 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg border border-white/20">
                            <i class="fab fa-tiktok text-4xl text-white"></i>
                        </div>
                        <h3 class="text-3xl font-bold text-white mb-2">TikTok</h3>
                        <p class="text-slate-400 font-medium tracking-wide">@PMB.UAD</p>
                    </div>
                </div>
                @endif
                <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4">Performa TikTok</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        {{ $settings['tiktok_description'] ?? 'Media utama untuk konten video pendek yang menghibur dan edukatif, berfokus pada tren terkini dan cerita mahasiswa.' }}
                    </p>
                    
                    <div class="mb-8">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center mb-4">
                            <i class="fas fa-chart-bar text-blue-500 mr-2"></i> Statistik Utama
                        </h4>
                        <div class="inline-flex items-center bg-slate-50 px-6 py-3 rounded-xl border border-slate-100">
                            <div class="mr-4 text-slate-400 text-2xl"><i class="fas fa-eye"></i></div>
                            <div>
                                <p class="text-sm text-slate-500 font-medium">Total Views</p>
                                <p class="text-2xl font-extrabold text-slate-800">{{ $settings['tiktok_views'] ?? '0' }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-lg font-bold text-slate-800 flex items-center mb-4">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Fokus Konten
                        </h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">Video Pendek Kreatif & Tren Terkini</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">A Day in My Life (Cerita Mahasiswa)</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-blue-500 mt-1 mr-3"></i>
                                <span class="text-slate-600">Edukasi Ringan seputar Jurusan & Kampus</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Kalender Section -->
<section class="py-16 bg-slate-50 border-t border-slate-100" id="kegiatan" x-data="calendarApp()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Kalender Kegiatan</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
            <p class="mt-4 text-slate-600">Jadwal kegiatan seluruh divisi Tim Kreatif PMB UAD</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Calendar -->
            <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                <!-- Header -->
                <div class="flex justify-between items-center mb-8">
                    <button @click="prevMonth()" class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center hover:bg-orange-200 transition-colors focus:outline-none">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="px-8 py-3 bg-orange-100 text-orange-800 font-bold rounded-full text-xl shadow-sm">
                        <span x-text="monthNames[currentMonth]"></span> <span x-text="currentYear"></span>
                    </div>
                    <button @click="nextMonth()" class="w-12 h-12 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center hover:bg-orange-200 transition-colors focus:outline-none">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
                
                <!-- Grid -->
                <div class="grid grid-cols-7 gap-4 mb-4 text-center text-sm font-bold text-slate-500">
                    <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                </div>
                <div class="grid grid-cols-7 gap-4 text-center">
                    <template x-for="(day, index) in blankdays" :key="'blank'+index">
                        <div class="p-3 border border-transparent"></div>
                    </template>
                    <template x-for="(date, dateIndex) in no_of_days" :key="dateIndex">
                        <div @click="selectDate(date)" class="p-3 border border-slate-100 rounded-xl cursor-pointer hover:shadow-md hover:-translate-y-1 transition-all min-h-[90px] flex flex-col items-center justify-start relative group" :class="{'bg-blue-50 border-blue-200 ring-2 ring-blue-500 ring-offset-2': isSelected(date)}">
                            <span class="text-xl font-medium text-slate-700" x-text="date" :class="{'text-blue-600 font-bold': isSelected(date)}"></span>
                            
                            <!-- Events Indicator -->
                            <div class="w-full mt-auto pt-2 space-y-1.5 px-1">
                                <template x-for="event in getEventsForDate(date).slice(0, 3)">
                                    <div class="h-1.5 w-full rounded-full" :style="'background-color: ' + getEventColor(event)"></div>
                                </template>
                                <template x-if="getEventsForDate(date).length > 3">
                                    <div class="text-[10px] text-slate-400 font-bold mt-1">+<span x-text="getEventsForDate(date).length - 3"></span></div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Right: Details & Legend -->
            <div class="space-y-6">
                <!-- Event Details -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8 min-h-[300px]">
                    <h3 class="font-bold text-slate-800 text-lg mb-6 flex items-center gap-3 border-b border-slate-100 pb-4">
                        <i class="far fa-calendar-alt text-blue-500 text-xl"></i> 
                        <span>Kegiatan <span x-text="selectedDate + ' ' + monthNames[currentMonth]"></span></span>
                    </h3>
                    
                    <div class="space-y-4 max-h-[400px] overflow-y-auto pr-2">
                        <template x-if="selectedEvents.length === 0">
                            <div class="text-center py-8">
                                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-3 text-2xl">
                                    <i class="fas fa-mug-hot"></i>
                                </div>
                                <p class="text-slate-500 italic text-sm">Tidak ada kegiatan di tanggal ini.</p>
                            </div>
                        </template>
                        <template x-for="event in selectedEvents">
                            <div class="border-l-4 pl-4 py-3 hover:bg-slate-50 rounded-r-lg transition-colors cursor-pointer" :style="'border-color: ' + getEventColor(event)">
                                <h4 class="font-bold text-slate-800" x-text="event.title"></h4>
                                <p class="text-sm text-slate-500 mt-1"><i class="far fa-clock mr-1 text-blue-500"></i> <span x-text="event.extendedProps.time"></span></p>
                                <template x-if="event.extendedProps.description">
                                    <p class="text-sm text-slate-600 mt-2" x-text="event.extendedProps.description"></p>
                                </template>
                                
                                <!-- Members list -->
                                <div class="mt-3 space-y-2">
                                    <template x-if="event.extendedProps.members.length === 0">
                                        <p class="text-xs text-slate-400 italic">Belum ada petugas</p>
                                    </template>
                                    <template x-for="member in event.extendedProps.members">
                                        <div class="flex items-start gap-2">
                                            <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0" :style="'background-color: ' + (member.pivot_division ? member.pivot_division.color_hex : '#64748B')"></div>
                                            <div class="flex flex-col">
                                                <span class="text-sm font-medium text-slate-700" x-text="member.full_name"></span>
                                                <span class="text-xs text-slate-500" x-text="member.pivot_division ? member.pivot_division.name : 'Anggota'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                                
                                <template x-if="event.extendedProps.external_link">
                                    <a :href="event.extendedProps.external_link" target="_blank" class="inline-block mt-3 text-xs bg-slate-100 hover:bg-blue-100 hover:text-blue-600 text-slate-700 px-3 py-1.5 rounded-lg transition-colors font-medium"><i class="fas fa-external-link-alt mr-1"></i> Buka Tautan</a>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Legend -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                    <h3 class="font-bold text-slate-800 text-lg mb-6">Legenda Kategori</h3>
                    <div class="space-y-3">
                        @foreach(\App\Models\Division::all() as $division)
                        <div class="flex items-center">
                            <div class="w-5 h-5 rounded-md mr-3 shadow-sm" style="background-color: {{ $division->color_hex }}"></div>
                            <span class="text-slate-600 font-medium">{{ $division->name }}</span>
                        </div>
                        @endforeach
                        <div class="flex items-center">
                            <div class="w-5 h-5 rounded-md mr-3 bg-blue-500 shadow-sm"></div>
                            <span class="text-slate-600 font-medium">Kegiatan Umum</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Berita/Kegiatan Terkini Section -->
<section class="py-16 bg-white border-t border-slate-100" id="berita">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Berita & Kegiatan Terkini</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
            <p class="mt-4 text-slate-600">Informasi dan hasil karya terbaru dari Tim Kreatif</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($latestNews as $news)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-lg transition-all hover:-translate-y-1 flex flex-col group">
                <div class="h-48 overflow-hidden relative">
                    @if($news->banner_image)
                        <img src="{{ asset('storage/'.$news->banner_image) }}" alt="{{ $news->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-300">
                            <i class="fas fa-image text-4xl"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full text-xs font-bold text-slate-700 shadow-sm">
                        <i class="far fa-calendar-alt mr-1 text-blue-500"></i> {{ $news->published_date->format('d M Y') }}
                    </div>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <h3 class="text-xl font-bold text-slate-800 mb-3 line-clamp-2 group-hover:text-blue-600 transition-colors">{{ $news->title }}</h3>
                    <p class="text-slate-600 text-sm mb-6 line-clamp-3">
                        {{ \Illuminate\Support\Str::limit(strip_tags($news->content), 120) }}
                    </p>
                    <div class="mt-auto border-t border-slate-50 pt-4">
                        <a href="{{ route('news.show', $news->slug) }}" class="inline-flex items-center text-blue-600 font-bold hover:text-blue-800 group-hover:text-blue-700 transition-colors">
                            Baca Selengkapnya <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition-transform"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">
                    <i class="fas fa-newspaper"></i>
                </div>
                <p class="text-slate-500 font-medium text-lg">Belum ada berita yang dipublikasikan.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    function calendarApp() {
        return {
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            currentMonth: new Date().getMonth(),
            currentYear: new Date().getFullYear(),
            no_of_days: [],
            blankdays: [],
            events: [],
            selectedDate: new Date().getDate(),
            selectedEvents: [],

            init() {
                this.getNoOfDays();
                this.fetchEvents();
                this.$watch('currentMonth', () => this.fetchEvents());
                this.$watch('currentYear', () => this.fetchEvents());
            },

            getNoOfDays() {
                let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                let dayOfWeek = new Date(this.currentYear, this.currentMonth).getDay();
                
                let blankdaysArray = [];
                for (var i = 1; i <= dayOfWeek; i++) {
                    blankdaysArray.push(i);
                }
                
                let daysArray = [];
                for (var i = 1; i <= daysInMonth; i++) {
                    daysArray.push(i);
                }
                
                this.blankdays = blankdaysArray;
                this.no_of_days = daysArray;
            },

            fetchEvents() {
                fetch(`/api/events/${this.currentYear}/${this.currentMonth + 1}`)
                    .then(res => res.json())
                    .then(data => {
                        this.events = data.map(event => {
                            return {
                                id: event.id,
                                title: event.title,
                                date: parseInt(event.event_date.split('-')[2]),
                                backgroundColor: this.getEventColor(event),
                                extendedProps: {
                                    time: event.formatted_time ? event.formatted_time : 'Seharian',
                                    description: event.description,
                                    members: event.members,
                                    external_link: event.external_link
                                }
                            };
                        });
                        this.updateSelectedEvents();
                    });
            },

            getEventsForDate(date) {
                return this.events.filter(e => e.date === date);
            },

            selectDate(date) {
                this.selectedDate = date;
                this.updateSelectedEvents();
            },

            isSelected(date) {
                return this.selectedDate === date;
            },

            updateSelectedEvents() {
                this.selectedEvents = this.getEventsForDate(this.selectedDate);
            },

            nextMonth() {
                if (this.currentMonth == 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
                this.getNoOfDays();
                this.selectedDate = 1;
            },

            prevMonth() {
                if (this.currentMonth == 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
                this.getNoOfDays();
                this.selectedDate = 1;
            },

            getEventColor(event) {
                if (event.members && event.members.length > 0 && event.members[0].pivot_division) {
                    return event.members[0].pivot_division.color_hex;
                }
                if (event.extendedProps && event.extendedProps.members && event.extendedProps.members.length > 0 && event.extendedProps.members[0].pivot_division) {
                    return event.extendedProps.members[0].pivot_division.color_hex;
                }
                return '#3B82F6';
            },

            getEventDivName(event) {
                if (event.extendedProps && event.extendedProps.members && event.extendedProps.members.length > 0 && event.extendedProps.members[0].pivot_division) {
                    return event.extendedProps.members[0].pivot_division.name;
                }
                return 'Kegiatan Umum';
            }
        }
    }
</script>
@endpush
