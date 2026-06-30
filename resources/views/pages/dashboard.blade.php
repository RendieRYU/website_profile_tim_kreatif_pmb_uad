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
<section class="py-16 bg-slate-50" id="pencapaian" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-slate-800">Statistik & Pencapaian</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Pendaftar UAD -->
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm text-center flex flex-col justify-center items-center relative overflow-hidden">
                <div class="w-full flex justify-between items-center mb-6 relative z-10">
                    <div class="text-left">
                        <h3 class="text-4xl font-extrabold text-slate-800">{{ $settings['registrant_count'] ?? 0 }}</h3>
                        <p class="text-slate-500 font-bold">Pendaftar UAD</p>
                    </div>
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl shadow-sm">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="relative h-[120px] w-full mt-auto">
                    <canvas id="registrantChart"></canvas>
                </div>
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
<section class="py-16 bg-white border-t border-slate-100" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ activeTab: 'total' }">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-slate-800">Performa Media Sosial</h2>
            <div class="w-24 h-1 bg-blue-600 mx-auto mt-4 rounded"></div>
        </div>

        <!-- Tabs -->
        <div class="flex justify-center mb-8">
            <div class="inline-flex bg-slate-100 rounded-full p-1 shadow-inner flex-wrap justify-center">
                <button @click="activeTab = 'total'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'total', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'total'}" class="px-6 md:px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fas fa-chart-pie mr-2 text-lg" :class="{'text-blue-500': activeTab === 'total'}"></i> Total
                </button>
                <button @click="activeTab = 'instagram'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'instagram', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'instagram'}" class="px-6 md:px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fab fa-instagram mr-2 text-lg" :class="{'text-pink-500': activeTab === 'instagram'}"></i> Instagram
                </button>
                <button @click="activeTab = 'tiktok'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'tiktok', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'tiktok'}" class="px-6 md:px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fab fa-tiktok mr-2 text-lg" :class="{'text-black': activeTab === 'tiktok'}"></i> TikTok
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col md:flex-row min-h-[400px]">
            
            <!-- Total View -->
            <div x-show="activeTab === 'total'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                <!-- Left: Chart -->
                <div class="md:w-5/12 bg-slate-50 border-r border-slate-100 p-8 flex flex-col justify-center relative">
                    <h4 class="text-lg font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 mr-2"></i> Perbandingan Followers / Views
                    </h4>
                    <div class="relative h-[250px] w-full">
                        <canvas id="totalChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4">Total Keseluruhan Live</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        Akumulasi keseluruhan tayangan langsung (Live) dari platform Instagram dan TikTok.
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Sesi Live</h4>
                            <p class="text-4xl font-extrabold text-blue-600">{{ $igTotalLive + $ttTotalLive }} <span class="text-lg font-medium text-slate-500">kali</span></p>
                        </div>
                        <div class="bg-purple-50 border border-purple-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Impresi / Penonton</h4>
                            <p class="text-4xl font-extrabold text-purple-600">{{ number_format($igTotalImpressions + $ttTotalImpressions, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instagram View -->
            <div x-show="activeTab === 'instagram'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                <!-- Left: Chart -->
                <div class="md:w-5/12 bg-pink-50/30 border-r border-slate-100 p-8 flex flex-col justify-center relative">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-chart-line text-pink-500 mr-2"></i> Grafik Followers IG
                        </h4>
                        <div class="text-right">
                            @php $latestIg = end($chartData['instagram']); @endphp
                            <span class="text-xl font-extrabold text-pink-600">{{ $latestIg ? number_format($latestIg, 0, ',', '.') : 0 }}</span>
                        </div>
                    </div>
                    <div class="relative h-[250px] w-full">
                        <canvas id="igChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4 flex items-center"><i class="fab fa-instagram text-pink-500 mr-3"></i> Performa Instagram</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        {{ $settings['instagram_description'] ?? 'Platform utama untuk membagikan info pendaftaran, dokumentasi acara, dan materi visual menarik seputar kehidupan kampus UAD.' }}
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Sesi Live IG</h4>
                            <p class="text-3xl font-extrabold text-slate-800">{{ $igTotalLive }} <span class="text-sm font-medium text-slate-500">kali</span></p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Impresi Live IG</h4>
                            <p class="text-3xl font-extrabold text-slate-800">{{ number_format($igTotalImpressions, 0, ',', '.') }}</p>
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
                <!-- Left: Chart -->
                <div class="md:w-5/12 bg-slate-50 border-r border-slate-100 p-8 flex flex-col justify-center relative">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-chart-line text-black mr-2"></i> Grafik Views / Followers TikTok
                        </h4>
                        <div class="text-right">
                            @php $latestTt = end($chartData['tiktok']); @endphp
                            <span class="text-xl font-extrabold text-slate-800">{{ $latestTt ? number_format($latestTt, 0, ',', '.') : 0 }}</span>
                        </div>
                    </div>
                    <div class="relative h-[250px] w-full">
                        <canvas id="tiktokChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-7/12 p-8 md:p-12 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4 flex items-center"><i class="fab fa-tiktok text-black mr-3"></i> Performa TikTok</h3>
                    <p class="text-slate-600 mb-8 leading-relaxed text-lg">
                        {{ $settings['tiktok_description'] ?? 'Media utama untuk konten video pendek yang menghibur dan edukatif, berfokus pada tren terkini dan cerita mahasiswa.' }}
                    </p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Sesi Live TikTok</h4>
                            <p class="text-3xl font-extrabold text-slate-800">{{ $ttTotalLive }} <span class="text-sm font-medium text-slate-500">kali</span></p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 p-6 rounded-2xl">
                            <h4 class="text-slate-500 font-medium mb-2">Total Impresi Live TikTok</h4>
                            <p class="text-3xl font-extrabold text-slate-800">{{ number_format($ttTotalImpressions, 0, ',', '.') }}</p>
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


<!-- Berita/Kegiatan Terkini Section -->
<section class="py-16 bg-white border-t border-slate-100" id="berita" data-aos="fade-up">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        // Prepare data from server
        const chartLabels = {!! json_encode($chartData['labels']) !!};
        const regData = {!! json_encode($chartData['registrant']) !!};
        const igData = {!! json_encode($chartData['instagram']) !!};
        const tiktokData = {!! json_encode($chartData['tiktok']) !!};

        // Shared chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                    titleColor: '#1e293b',
                    bodyColor: '#334155',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b' }
                },
                y: {
                    border: { display: false },
                    grid: { color: '#f1f5f9' },
                    ticks: { font: { family: "'Inter', sans-serif" }, color: '#94a3b8' }
                }
            }
        };

        // Total Combined Chart
        if(document.getElementById('totalChart')) {
            new Chart(document.getElementById('totalChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Followers IG',
                            data: igData,
                            borderColor: '#ec4899',
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#ec4899',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.4
                        },
                        {
                            label: 'TikTok Views',
                            data: tiktokData,
                            borderColor: '#0f172a',
                            backgroundColor: 'transparent',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#0f172a',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: false,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { 
                            display: true,
                            position: 'top',
                            labels: {
                                font: { family: "'Inter', sans-serif" },
                                color: '#475569',
                                usePointStyle: true,
                                boxWidth: 8
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(255, 255, 255, 0.9)',
                            titleColor: '#1e293b',
                            bodyColor: '#334155',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 10,
                            displayColors: true,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b' }
                        },
                        y: {
                            border: { display: false },
                            grid: { color: '#f1f5f9' },
                            ticks: { font: { family: "'Inter', sans-serif" }, color: '#94a3b8' }
                        }
                    }
                }
            });
        }

        // Registrant Chart
        if(document.getElementById('registrantChart')) {
            new Chart(document.getElementById('registrantChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Pendaftar',
                        data: regData,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: commonOptions
            });
        }

        // Instagram Chart
        if(document.getElementById('igChart')) {
            new Chart(document.getElementById('igChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Followers IG',
                        data: igData,
                        borderColor: '#ec4899',
                        backgroundColor: 'rgba(236, 72, 153, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#ec4899',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: commonOptions
            });
        }

        // TikTok Chart
        if(document.getElementById('tiktokChart')) {
            new Chart(document.getElementById('tiktokChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'TikTok Metrics',
                        data: tiktokData,
                        borderColor: '#0f172a',
                        backgroundColor: 'rgba(15, 23, 42, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#0f172a',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: commonOptions
            });
        }
    });


</script>

@endpush
