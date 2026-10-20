@extends('layouts.app')

@section('content')
<x-public-blur-lock>
<div class="py-8 bg-slate-50 min-h-screen" x-data="publicProjectKanban()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-tasks text-emerald-500"></i> Papan Projek Tim Kreatif
                </h1>
                <p class="text-slate-500 text-sm mt-1">Progres kegiatan & alur kerja publik (Periode {{ $activePeriod->name ?? '-' }})</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" x-model="searchQuery" placeholder="Cari projek..." class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>
                <a href="{{ route('calendar') }}" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-100 rounded-xl text-xs font-bold transition-colors shrink-0">
                    <i class="far fa-calendar-alt mr-1"></i> Lihat Kalender
                </a>
            </div>
        </div>

        <!-- PUBLIC KANBAN BOARD (3 COLUMNS) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
            
            <!-- COLUMN 1: TO-DO -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4 min-h-[500px]">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                        <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wide">TO-DO</h3>
                    </div>
                    <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded-full text-xs font-black" x-text="getColumnEvents('todo').length"></span>
                </div>

                <div class="space-y-3">
                    <template x-for="event in getColumnEvents('todo')" :key="event.id">
                        <div @click="openDetailModal(event)"
                             :class="isKunjungan(event) ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' : 'bg-slate-50 text-slate-800 border-slate-200/80 hover:bg-white hover:shadow-md'"
                             class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                            
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex flex-wrap gap-1">
                                    <template x-if="isKunjungan(event)">
                                        <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                    </template>
                                    <template x-for="cat in event.categories">
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                    </template>
                                </div>
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 shrink-0" x-text="event.status"></span>
                            </div>

                            <h4 class="font-extrabold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                            <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                                <i class="far fa-calendar-alt text-[11px]"></i>
                                <span x-text="formatDate(event.event_date)"></span>
                                <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                            </div>

                            <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-200/60'">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                    </template>
                                    <template x-if="event.members.length > 3">
                                        <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Detail <i class="fas fa-chevron-right text-[8px]"></i></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="getColumnEvents('todo').length === 0">
                        <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-100 rounded-2xl">
                            Belum ada projek dalam To-Do.
                        </div>
                    </template>
                </div>
            </div>

            <!-- COLUMN 2: ON-GOING -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4 min-h-[500px]">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wide">ON-GOING</h3>
                    </div>
                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs font-black" x-text="getColumnEvents('ongoing').length"></span>
                </div>

                <div class="space-y-3">
                    <template x-for="event in getColumnEvents('ongoing')" :key="event.id">
                        <div @click="openDetailModal(event)"
                             :class="isKunjungan(event) ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' : 'bg-slate-50 text-slate-800 border-slate-200/80 hover:bg-white hover:shadow-md'"
                             class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                            
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex flex-wrap gap-1">
                                    <template x-if="isKunjungan(event)">
                                        <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                    </template>
                                    <template x-for="cat in event.categories">
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                    </template>
                                </div>
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 shrink-0">ACC</span>
                            </div>

                            <h4 class="font-extrabold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                            <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                                <i class="far fa-calendar-alt text-[11px]"></i>
                                <span x-text="formatDate(event.event_date)"></span>
                                <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                            </div>

                            <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-200/60'">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                    </template>
                                    <template x-if="event.members.length > 3">
                                        <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Detail <i class="fas fa-chevron-right text-[8px]"></i></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="getColumnEvents('ongoing').length === 0">
                        <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-100 rounded-2xl">
                            Belum ada projek dalam On-Going.
                        </div>
                    </template>
                </div>
            </div>

            <!-- COLUMN 3: COMPLETE -->
            <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm space-y-4 min-h-[500px]">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <h3 class="font-extrabold text-slate-800 text-sm uppercase tracking-wide">COMPLETE</h3>
                    </div>
                    <span class="px-2.5 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-black" x-text="getColumnEvents('complete').length"></span>
                </div>

                <div class="space-y-3">
                    <template x-for="event in getColumnEvents('complete')" :key="event.id">
                        <div @click="openDetailModal(event)"
                             :class="isKunjungan(event) ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' : 'bg-slate-50 text-slate-800 border-slate-200/80 hover:bg-white hover:shadow-md'"
                             class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                            
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex flex-wrap gap-1">
                                    <template x-if="isKunjungan(event)">
                                        <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                    </template>
                                    <template x-for="cat in event.categories">
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                    </template>
                                </div>
                                <span class="text-[9px] uppercase font-black px-1.5 py-0.5 rounded bg-green-100 text-green-800 shrink-0">SELESAI</span>
                            </div>

                            <h4 class="font-extrabold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                            <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                                <i class="far fa-calendar-alt text-[11px]"></i>
                                <span x-text="formatDate(event.event_date)"></span>
                                <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                            </div>

                            <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-200/60'">
                                <div class="flex flex-wrap gap-1">
                                    <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                    </template>
                                    <template x-if="event.members.length > 3">
                                        <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                    </template>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Detail <i class="fas fa-chevron-right text-[8px]"></i></span>
                            </div>
                        </div>
                    </template>

                    <template x-if="getColumnEvents('complete').length === 0">
                        <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-100 rounded-2xl">
                            Belum ada projek dalam Complete.
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- PUBLIC DETAIL MODAL -->
        <div x-show="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="selectedEvent = null"></div>
            <div class="bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 relative z-10">
                <button @click="selectedEvent = null" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>

                <div class="flex items-center gap-2 mb-2" x-show="isKunjungan(selectedEvent)">
                    <span class="px-2.5 py-0.5 bg-indigo-600 text-white font-bold text-xs rounded-full uppercase"><i class="fas fa-bus mr-1"></i> Kegiatan Kunjungan</span>
                </div>

                <h3 class="text-2xl font-extrabold text-slate-900 mb-4 pr-6 leading-snug" x-text="selectedEvent?.title"></h3>

                <div class="space-y-3 text-sm mb-6">
                    <div class="flex items-center gap-3">
                        <span class="w-24 text-slate-400 font-medium">Tanggal:</span>
                        <span class="font-bold text-slate-800" x-text="formatDate(selectedEvent?.event_date)"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-24 text-slate-400 font-medium">Waktu:</span>
                        <span class="font-bold text-slate-800" x-text="selectedEvent?.formatted_time || 'Seharian'"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="w-24 text-slate-400 font-medium">Status:</span>
                        <span class="px-2.5 py-0.5 rounded text-xs font-black uppercase tracking-wider"
                              :class="{
                                  'bg-amber-100 text-amber-800': ['ide', 'terjadwal', 'ditunda'].includes(selectedEvent?.status),
                                  'bg-blue-100 text-blue-800': selectedEvent?.status === 'acc',
                                  'bg-green-100 text-green-800': selectedEvent?.status === 'selesai',
                                  'bg-rose-100 text-rose-800': selectedEvent?.status === 'ditolak'
                              }" x-text="selectedEvent?.status"></span>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="w-24 text-slate-400 font-medium mt-0.5">Petugas:</span>
                        <div class="flex-1 flex flex-wrap gap-1">
                            <template x-for="m in selectedEvent?.members" :key="m.id">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded-lg" x-text="m.full_name + (m.pivot && m.pivot.role ? ' (' + m.pivot.role + ')' : '')"></span>
                            </template>
                            <template x-if="!selectedEvent?.members || selectedEvent?.members.length === 0">
                                <span class="text-slate-400 italic">Belum ada petugas</span>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-start gap-3" x-show="getEventLinks(selectedEvent).length > 0">
                        <span class="w-24 text-slate-400 font-medium mt-0.5">Tautan:</span>
                        <div class="flex-1 flex flex-wrap gap-1.5">
                            <template x-for="(l, i) in getEventLinks(selectedEvent)" :key="i">
                                <a :href="l.url" target="_blank" class="px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 text-xs font-bold rounded-lg flex items-center gap-1">
                                    <i class="fas fa-external-link-alt text-[10px]"></i> <span x-text="l.title || 'Link'"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                    <div class="flex items-start gap-3" x-show="selectedEvent?.description">
                        <span class="w-24 text-slate-400 font-medium mt-0.5">Deskripsi:</span>
                        <p class="flex-1 text-xs text-slate-600 leading-relaxed whitespace-pre-wrap" x-text="selectedEvent?.description"></p>
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-slate-100">
                    <button type="button" @click="selectedEvent = null" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
</x-public-blur-lock>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('publicProjectKanban', () => ({
            events: {!! json_encode($events) !!},
            searchQuery: '',
            selectedEvent: null,
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

            getColumnEvents(colName) {
                return this.events.filter(event => {
                    if (this.searchQuery.trim() !== '') {
                        let q = this.searchQuery.toLowerCase();
                        if (!event.title.toLowerCase().includes(q)) return false;
                    }

                    if (colName === 'todo') {
                        return ['ide', 'terjadwal', 'ditunda', 'ditolak'].includes(event.status);
                    } else if (colName === 'ongoing') {
                        return event.status === 'acc';
                    } else if (colName === 'complete') {
                        return event.status === 'selesai';
                    }
                    return false;
                });
            },

            isKunjungan(event) {
                if (!event) return false;
                let titleMatch = event.title && event.title.toLowerCase().includes('kunjungan');
                let catMatch = event.categories && event.categories.some(c => c.name && c.name.toLowerCase().includes('kunjungan'));
                return titleMatch || catMatch;
            },

            getEventLinks(event) {
                if (!event) return [];
                if (Array.isArray(event.links) && event.links.length > 0) return event.links;
                if (event.link) return [{ title: 'Tautan Utama', url: event.link }];
                return [];
            },

            openDetailModal(event) {
                this.selectedEvent = event;
            },

            formatDate(dateString) {
                if (!dateString) return '';
                let d = new Date(dateString);
                return d.getDate() + ' ' + this.monthNames[d.getMonth()] + ' ' + d.getFullYear();
            }
        }));
    });
</script>
@endpush
