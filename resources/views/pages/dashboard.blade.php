@extends('layouts.app')

@section('content')

<section class="relative w-full aspect-video flex items-center justify-center text-white overflow-hidden bg-cover bg-[center_30%]"
         style="background-image: url('{{ isset($settings['team_photo']) && $settings['team_photo'] ? asset('storage/'.$settings['team_photo']) : '' }}');">
    <!-- Dark overlay to ensure text readability -->
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-[1px]"></div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight mb-8 drop-shadow-md">Tim Kreatif PMB UAD</h1>
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
                        <h3 class="text-4xl font-extrabold text-slate-800">{{ number_format($latestRegistrant, 0, ',', '.') }}</h3>
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
            <style>[x-cloak] { display: none !important; }</style>
            <div class="md:col-span-2 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm" x-data="{ showModal: false, modalTitle: '', modalEvents: [] }">
                <h3 class="text-xl font-bold text-slate-800 mb-6 flex items-center"><i class="fas fa-chart-pie text-blue-500 mr-3"></i> Rekap Kegiatan per Divisi</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($divisions as $div)
                    @php
                        $count = $divisionEventCounts->get($div->id, 0);
                        $events = isset($divisionEvents[$div->id]) ? $divisionEvents[$div->id]->values() : collect();
                    @endphp
                    <div class="p-4 rounded-2xl border border-slate-100 hover:shadow-md hover:border-blue-100 transition-all bg-slate-50 cursor-pointer group"
                         @click="showModal = true; modalTitle = '{{ $div->name }}'; modalEvents = {{ json_encode($events) }}">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2 group-hover:scale-125 transition-transform" style="background-color: {{ $div->color_hex }}"></div>
                                <span class="font-bold text-slate-700 group-hover:text-blue-600 transition-colors">{{ $div->name }}</span>
                            </div>
                            <span class="text-slate-400 group-hover:text-blue-500 transition-colors text-[11px] flex items-center gap-1 font-medium">Detail <i class="fas fa-chevron-right text-[8px]"></i></span>
                        </div>
                        <div class="flex items-end gap-2 mt-3">
                            <span class="text-3xl font-extrabold text-slate-800 leading-none">{{ $count }}</span>
                            <span class="text-sm text-slate-500 font-medium mb-1">Kegiatan</span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Modal Detail Kegiatan -->
                <div x-show="showModal" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm flex items-center justify-center z-50 p-4" x-transition x-cloak>
                    <div class="bg-white rounded-3xl max-w-lg w-full max-h-[85vh] overflow-hidden shadow-2xl flex flex-col border border-slate-100" @click.outside="showModal = false">
                        <!-- Modal Header -->
                        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                            <h4 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                                <i class="fas fa-calendar-check text-blue-600"></i>
                                <span x-text="'Kegiatan ' + modalTitle"></span>
                            </h4>
                            <button @click="showModal = false" class="text-slate-400 hover:text-slate-600 focus:outline-none transition-colors text-xl">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <!-- Modal Body -->
                        <div class="p-6 overflow-y-auto flex-1 space-y-3 bg-white">
                            <template x-if="modalEvents.length === 0">
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-calendar-times text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 italic text-sm">Belum ada kegiatan yang tercatat untuk divisi ini.</p>
                                </div>
                            </template>
                            <template x-for="event in modalEvents" :key="event.id">
                                <div class="p-4 rounded-xl border transition-colors flex items-center justify-between gap-4"
                                     :class="event.title && event.title.toLowerCase().includes('kunjungan') 
                                        ? 'bg-indigo-600 text-white border-indigo-700 shadow-sm' 
                                        : 'border-slate-100 bg-slate-50 hover:bg-slate-100/50'">
                                    <div class="min-w-0 flex-1">
                                        <p class="font-bold text-sm leading-snug truncate" :class="event.title && event.title.toLowerCase().includes('kunjungan') ? 'text-white' : 'text-slate-800'" x-text="event.title"></p>
                                        <p class="text-xs mt-1 flex items-center gap-1.5" :class="event.title && event.title.toLowerCase().includes('kunjungan') ? 'text-indigo-200' : 'text-slate-500'">
                                            <i class="far fa-calendar text-[11px]"></i>
                                            <span x-text="new Date(event.event_date).toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})"></span>
                                        </p>
                                    </div>
                                    <template x-if="event.title && event.title.toLowerCase().includes('kunjungan')">
                                        <span class="bg-indigo-800 text-indigo-100 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                    </template>
                                    <template x-if="!event.title || !event.title.toLowerCase().includes('kunjungan')">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0" 
                                              :class="{
                                                  'bg-blue-100 text-blue-800 border border-blue-200': event.status === 'acc',
                                                  'bg-green-100 text-green-800 border border-green-200': event.status === 'selesai',
                                                  'bg-yellow-100 text-yellow-800 border border-yellow-200': event.status === 'ide',
                                                  'bg-red-100 text-red-800 border border-red-200': event.status === 'ditunda',
                                                  'bg-purple-100 text-purple-800 border border-purple-200': event.status === 'terjadwal',
                                                  'bg-rose-100 text-rose-800 border border-rose-200': event.status === 'ditolak'
                                              }"
                                              x-text="event.status">
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Performance -->
<section class="py-16 bg-white border-t border-slate-100" data-aos="fade-up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="socialDashboard()">
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
                <button @click="activeTab = 'live'" :class="{'bg-white text-blue-600 shadow-sm font-bold': activeTab === 'live', 'text-slate-500 hover:text-slate-700 font-medium': activeTab !== 'live'}" class="px-6 md:px-8 py-2.5 rounded-full text-sm transition-all flex items-center">
                    <i class="fas fa-video mr-2 text-lg" :class="{'text-red-500': activeTab === 'live'}"></i> Live
                </button>
            </div>
        </div>

        <!-- Content Area -->
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden flex flex-col md:flex-row min-h-[400px]">
            
            <!-- 1. Total View (No Graph, Full Width) -->
            <div x-show="activeTab === 'total'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="w-full p-8 md:p-12 flex flex-col justify-center">
                <div class="text-center max-w-3xl mx-auto mb-10">
                    <h3 class="text-3xl font-bold text-slate-800 mb-3">Total Keseluruhan Performa</h3>
                    <p class="text-slate-600 leading-relaxed text-base">
                        Ringkasan akumulasi tayangan langsung (Live), postingan konten, dan pertumbuhan pengikut di platform Instagram & TikTok.
                    </p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-blue-50/70 border border-blue-100 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Total Sesi Live</h4>
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-broadcast-tower"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-blue-600">{{ $igTotalLive + $ttTotalLive }} <span class="text-sm font-medium text-slate-500">kali</span></p>
                    </div>

                    <div class="bg-purple-50/70 border border-purple-100 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Total Impresi / Penonton</h4>
                            <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-purple-600">{{ number_format($igTotalImpressions + $ttTotalImpressions, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-pink-50/70 border border-pink-100 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Postingan Instagram</h4>
                            <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center">
                                <i class="fab fa-instagram"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-pink-600">{{ number_format($grandTotalPosts, 0, ',', '.') }} <span class="text-sm font-medium text-slate-500">post</span></p>
                    </div>

                    <div class="bg-pink-50/70 border border-pink-100 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Followers Baru Instagram</h4>
                            <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-pink-600">+{{ number_format($grandTotalNewFollowers, 0, ',', '.') }}</p>
                    </div>

                    <div class="bg-slate-100/70 border border-slate-200 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Postingan TikTok</h4>
                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center">
                                <i class="fab fa-tiktok"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-slate-800">{{ number_format($tiktokGrandTotalPosts, 0, ',', '.') }} <span class="text-sm font-medium text-slate-500">post</span></p>
                    </div>

                    <div class="bg-slate-100/70 border border-slate-200 p-6 rounded-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-slate-600 font-medium text-sm">Followers Baru TikTok</h4>
                            <div class="w-10 h-10 rounded-full bg-slate-200 text-slate-800 flex items-center justify-center">
                                <i class="fas fa-user-plus"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold text-slate-800">+{{ number_format($tiktokGrandTotalNewFollowers, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- 2. Instagram View -->
            <div x-show="activeTab === 'instagram'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                <!-- Left: Chart -->
                <div class="md:w-6/12 bg-pink-50/10 border-r border-slate-100 p-6 flex flex-col justify-center relative">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-chart-line text-pink-500 mr-2"></i> Grafik Performa Instagram
                        </h4>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="igChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-6/12 p-8 flex flex-col justify-center">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <h3 class="text-3xl font-bold text-slate-800 flex items-center"><i class="fab fa-instagram text-pink-500 mr-3"></i> Performa Instagram</h3>
                        
                        <!-- Month Dropdown filter -->
                        <div class="flex items-center gap-2">
                            <select x-model="selectedIgMonth" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 font-medium focus:outline-none focus:border-pink-500 focus:bg-white transition-all cursor-pointer">
                                <option value="">Seluruh Bulan</option>
                                @foreach($instagramMonthly as $value => $data)
                                    <option value="{{ $value }}">{{ $data['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <p class="text-slate-600 mb-6 leading-relaxed text-sm">
                        {{ $settings['instagram_description'] ?? 'Platform utama untuk membagikan info pendaftaran, dokumentasi acara, dan materi visual menarik seputar kehidupan kampus UAD.' }}
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50/60 border border-slate-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Postingan</h4>
                            <p class="text-2xl font-extrabold text-slate-800" x-text="igTotalPosts + ' kali'"></p>
                        </div>
                        <div class="bg-slate-50/60 border border-slate-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Pertumbuhan Followers</h4>
                            <p class="text-2xl font-extrabold text-slate-800" x-text="igNewFollowers"></p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center mb-3">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Fokus Konten
                        </h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Update Informasi PMB & Jadwal Pendaftaran
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Dokumentasi Event & Kegiatan Kampus
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Konten Interaktif (QnA, Polling, Live)
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. TikTok View -->
            <div x-show="activeTab === 'tiktok'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                <!-- Left: Chart -->
                <div class="md:w-6/12 bg-slate-50 border-r border-slate-100 p-6 flex flex-col justify-center relative">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-chart-line text-slate-900 mr-2"></i> Grafik Performa TikTok
                        </h4>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="tiktokChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-6/12 p-8 flex flex-col justify-center">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <h3 class="text-3xl font-bold text-slate-800 flex items-center"><i class="fab fa-tiktok text-black mr-3"></i> Performa TikTok</h3>
                        
                        <!-- Month Dropdown filter -->
                        <div class="flex items-center gap-2">
                            <select x-model="selectedTtMonth" class="px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-700 font-medium focus:outline-none focus:border-slate-800 focus:bg-white transition-all cursor-pointer">
                                <option value="">Seluruh Bulan</option>
                                @foreach($tiktokMonthly as $value => $data)
                                    <option value="{{ $value }}">{{ $data['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <p class="text-slate-600 mb-6 leading-relaxed text-sm">
                        {{ $settings['tiktok_description'] ?? 'Media utama untuk konten video pendek yang menghibur dan edukatif, berfokus pada tren terkini dan cerita mahasiswa.' }}
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50/60 border border-slate-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Postingan</h4>
                            <p class="text-2xl font-extrabold text-slate-800" x-text="ttTotalPosts + ' kali'"></p>
                        </div>
                        <div class="bg-slate-50/60 border border-slate-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Pertumbuhan Followers</h4>
                            <p class="text-2xl font-extrabold text-slate-800" x-text="ttNewFollowers"></p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center mb-3">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Fokus Konten
                        </h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Video Pendek Kreatif & Tren Terkini
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> A Day in My Life (Cerita Mahasiswa)
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Edukasi Ringan seputar Jurusan & Kampus
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. Live View -->
            <div x-show="activeTab === 'live'" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="display: none;"
                 class="flex flex-col md:flex-row min-h-[400px] w-full">
                <!-- Left: Chart -->
                <div class="md:w-6/12 bg-slate-50 border-r border-slate-100 p-6 flex flex-col justify-center relative">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-lg font-bold text-slate-800 flex items-center">
                            <i class="fas fa-chart-line text-red-500 mr-2"></i> Grafik Impresi Live
                        </h4>
                    </div>
                    <div class="relative h-[280px] w-full">
                        <canvas id="liveChart"></canvas>
                    </div>
                </div>
                <!-- Right: Details -->
                <div class="md:w-6/12 p-8 flex flex-col justify-center">
                    <h3 class="text-3xl font-bold text-slate-800 mb-4 flex items-center"><i class="fas fa-video text-red-500 mr-3"></i> Performa Live</h3>
                    <p class="text-slate-600 mb-6 leading-relaxed text-sm">
                        Akumulasi keseluruhan tayangan langsung (Live) dari platform Instagram dan TikTok.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div class="bg-blue-50/60 border border-blue-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Sesi Live</h4>
                            <p class="text-2xl font-extrabold text-blue-600">{{ $igTotalLive + $ttTotalLive }} <span class="text-sm font-medium text-slate-500">kali</span></p>
                        </div>
                        <div class="bg-purple-50/60 border border-purple-100 p-5 rounded-2xl">
                            <h4 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Total Impresi / Penonton</h4>
                            <p class="text-2xl font-extrabold text-purple-600">{{ number_format($igTotalImpressions + $ttTotalImpressions, 0, ',', '.') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 flex items-center mb-3">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i> Fokus Konten Live
                        </h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Q&A Pendaftaran PMB & Beasiswa
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Live Virtual Tour Kampus & Fasilitas
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-check text-blue-500 mr-2"></i> Bincang Santai bersama Mahasiswa Prestasi
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
                <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-newspaper text-3xl text-slate-400"></i>
                </div>
                <h3 class="text-lg font-medium text-slate-600 mb-1">Belum ada berita</h3>
                <p class="text-slate-500 text-sm">Belum ada berita atau artikel terbaru yang dipublikasikan.</p>
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
        Alpine.data('socialDashboard', () => ({
            activeTab: 'total',
            selectedIgMonth: '',
            selectedTtMonth: '',
            
            // Raw stats Instagram
            igMonthlyStats: {!! json_encode($instagramMonthly) !!},
            igDailyStats: {!! json_encode($instagramDaily) !!},
            igYearlyLabels: {!! json_encode($yearlyLabels) !!},
            igYearlyPosts: {!! json_encode($yearlyPosts) !!},
            igYearlyFollowers: {!! json_encode($yearlyFollowers) !!},

            // Raw stats TikTok
            ttMonthlyStats: {!! json_encode($tiktokMonthly) !!},
            ttDailyStats: {!! json_encode($tiktokDaily) !!},
            ttYearlyLabels: {!! json_encode($yearlyLabels) !!},
            ttYearlyPosts: {!! json_encode($tiktokYearlyPosts) !!},
            ttYearlyFollowers: {!! json_encode($tiktokYearlyFollowers) !!},

            // Display variables IG
            igTotalPosts: {{ $grandTotalPosts }},
            igNewFollowers: {{ $grandTotalNewFollowers }},

            // Display variables TikTok
            ttTotalPosts: {{ $tiktokGrandTotalPosts }},
            ttNewFollowers: {{ $tiktokGrandTotalNewFollowers }},

            init() {
                this.$watch('selectedIgMonth', (val) => {
                    this.updateIgStats(val);
                    this.updateIgChart(val);
                });

                this.$watch('selectedTtMonth', (val) => {
                    this.updateTtStats(val);
                    this.updateTtChart(val);
                });
            },

            updateIgStats(month) {
                if (month === '') {
                    this.igTotalPosts = {{ $grandTotalPosts }};
                    this.igNewFollowers = {{ $grandTotalNewFollowers }};
                } else if (this.igMonthlyStats[month]) {
                    this.igTotalPosts = this.igMonthlyStats[month].total_posts;
                    this.igNewFollowers = this.igMonthlyStats[month].new_followers;
                } else {
                    this.igTotalPosts = 0;
                    this.igNewFollowers = 0;
                }
            },

            updateIgChart(month) {
                if (!window.igChartInstance) return;

                let labels = [];
                let postsData = [];
                let followersData = [];

                if (month === '') {
                    labels = this.igYearlyLabels;
                    postsData = this.igYearlyPosts;
                    followersData = this.igYearlyFollowers;
                } else if (this.igDailyStats[month]) {
                    labels = this.igDailyStats[month].labels;
                    postsData = this.igDailyStats[month].posts;
                    followersData = this.igDailyStats[month].followers;
                }

                window.igChartInstance.data.labels = labels;
                window.igChartInstance.data.datasets[0].data = postsData;
                window.igChartInstance.data.datasets[1].data = followersData;
                window.igChartInstance.update();
            },

            updateTtStats(month) {
                if (month === '') {
                    this.ttTotalPosts = {{ $tiktokGrandTotalPosts }};
                    this.ttNewFollowers = {{ $tiktokGrandTotalNewFollowers }};
                } else if (this.ttMonthlyStats[month]) {
                    this.ttTotalPosts = this.ttMonthlyStats[month].total_posts;
                    this.ttNewFollowers = this.ttMonthlyStats[month].new_followers;
                } else {
                    this.ttTotalPosts = 0;
                    this.ttNewFollowers = 0;
                }
            },

            updateTtChart(month) {
                if (!window.ttChartInstance) return;

                let labels = [];
                let postsData = [];
                let followersData = [];

                if (month === '') {
                    labels = this.ttYearlyLabels;
                    postsData = this.ttYearlyPosts;
                    followersData = this.ttYearlyFollowers;
                } else if (this.ttDailyStats[month]) {
                    labels = this.ttDailyStats[month].labels;
                    postsData = this.ttDailyStats[month].posts;
                    followersData = this.ttDailyStats[month].followers;
                }

                window.ttChartInstance.data.labels = labels;
                window.ttChartInstance.data.datasets[0].data = postsData;
                window.ttChartInstance.data.datasets[1].data = followersData;
                window.ttChartInstance.update();
            }
        }));

        // Prepare data from server
        const chartLabels = {!! json_encode($chartData['labels']) !!};
        const regData = {!! json_encode($chartData['registrant']) !!};
        const igImpressionsData = {!! json_encode($chartData['instagram_impressions']) !!};
        const tiktokImpressionsData = {!! json_encode($chartData['tiktok_impressions']) !!};

        // Shared chart options
        const commonOptions = {
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
        };

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
            window.igChartInstance = new Chart(document.getElementById('igChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($yearlyLabels) !!},
                    datasets: [
                        {
                            label: 'Total Postingan',
                            data: {!! json_encode($yearlyPosts) !!},
                            borderColor: '#ef4444',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#ef4444',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Pertumbuhan Follower',
                            data: {!! json_encode($yearlyFollowers) !!},
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            borderWidth: 3,
                            pointBackgroundColor: '#ffffff',
                            pointBorderColor: '#3b82f6',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: commonOptions
            });
        }

        // TikTok Chart
        if(document.getElementById('tiktokChart')) {
            window.ttChartInstance = new Chart(document.getElementById('tiktokChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: {!! json_encode($yearlyLabels) !!},
                    datasets: [
                        {
                            label: 'Total Postingan',
                            data: {!! json_encode($tiktokYearlyPosts) !!},
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
                        },
                        {
                            label: 'Pertumbuhan Follower',
                            data: {!! json_encode($tiktokYearlyFollowers) !!},
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
                        }
                    ]
                },
                options: commonOptions
            });
        }

        // Live Chart
        if(document.getElementById('liveChart')) {
            new Chart(document.getElementById('liveChart').getContext('2d'), {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Live Instagram',
                            data: igImpressionsData,
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
                        },
                        {
                            label: 'Live TikTok',
                            data: tiktokImpressionsData,
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
                        }
                    ]
                },
                options: commonOptions
            });
        }
    });
</script>
@endpush
