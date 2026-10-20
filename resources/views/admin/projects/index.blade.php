@extends('layouts.admin')

@section('content')
<div x-data="projectKanban()" class="space-y-6">
    <!-- Header Title & Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-tasks text-emerald-500"></i> Papan Projek (Kanban Board)
            </h1>
            <p class="text-slate-500 text-sm mt-1">Kelola progres kegiatan (Periode {{ $activePeriod->name }}) • Status tersinkronisasi otomatis dengan Kalender</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Search Input -->
            <div class="relative flex-1 md:w-64">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari projek/kegiatan..." class="w-full pl-9 pr-3 py-2 bg-white border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500 shadow-xs">
            </div>

            <a href="{{ route('admin.events.index') }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg transition-colors flex items-center justify-center gap-1.5 text-xs font-semibold">
                <i class="fas fa-calendar-alt"></i> Buka Kalender
            </a>

            <a href="{{ route('admin.events.create') }}" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center justify-center gap-1.5 text-xs font-semibold shadow-sm">
                <i class="fas fa-plus"></i> Tambah Projek
            </a>
        </div>
    </div>

    <!-- Instructions / Hint -->
    <div class="bg-blue-50/70 border border-blue-200/80 p-3.5 rounded-xl text-xs text-blue-800 flex items-center justify-between gap-3">
        <div class="flex items-center gap-2">
            <i class="fas fa-info-circle text-blue-600 text-sm shrink-0"></i>
            <span><strong>Tips Drag & Drop:</strong> Geser (*drag*) kartu projek antar kolom untuk memperbarui statusnya secara otomatis di database dan Kalender.</span>
        </div>
        <span class="text-[11px] bg-blue-600 text-white font-bold px-2 py-0.5 rounded-full shrink-0">Realtime Sync</span>
    </div>

    <!-- KANBAN BOARD 3 COLUMNS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        
        <!-- COLUMN 1: TO-DO -->
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200/80 space-y-3 min-h-[500px] flex flex-col"
             @dragover.prevent="dragOverColumn = 'todo'"
             @dragleave="dragOverColumn = null"
             @drop.prevent="handleDropToColumn('todo')"
             :class="{'!border-amber-500 !bg-amber-50/60 ring-2 ring-amber-400 border-dashed': dragOverColumn === 'todo'}">
            
            <!-- Column Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                    <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">TO-DO</h3>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-800 rounded-full text-xs font-extrabold" x-text="getColumnEvents('todo').length"></span>
                </div>
                <a href="{{ route('admin.events.create') }}?status=ide" class="text-xs text-slate-400 hover:text-amber-600 font-semibold flex items-center gap-1" title="Tambah To-Do">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <!-- Event Cards List -->
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar max-h-[700px] pr-0.5">
                <template x-for="event in getColumnEvents('todo')" :key="event.id">
                    <div draggable="true"
                         @dragstart="handleDragStart($event, event)"
                         @dragend="draggedEvent = null; dragOverColumn = null;"
                         @click="openDetailModal(event)"
                         :class="isKunjungan(event) 
                            ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' 
                            : 'bg-white text-slate-800 border-slate-200 hover:border-blue-400'"
                         class="p-4 rounded-xl border shadow-xs hover:shadow-md transition-all cursor-grab active:cursor-grabbing space-y-2 relative group">
                        
                        <!-- Card Header Tags -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex flex-wrap gap-1">
                                <template x-if="isKunjungan(event)">
                                    <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                </template>
                                <template x-for="cat in event.categories">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                </template>
                            </div>
                            <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 shrink-0" x-text="event.status"></span>
                        </div>

                        <!-- Card Title -->
                        <h4 class="font-bold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                        <!-- Date & Time -->
                        <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                            <i class="far fa-calendar-alt text-[11px]"></i>
                            <span x-text="formatDate(event.event_date)"></span>
                            <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                        </div>

                        <!-- Card Footer: Members & Quick Edit -->
                        <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-100'">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                    <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                </template>
                                <template x-if="event.members.length > 3">
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                </template>
                            </div>

                            <a :href="'/admin/events/' + event.id + '/edit'" @click.stop class="text-xs p-1 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="getColumnEvents('todo').length === 0">
                    <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-200 rounded-xl">
                        Tidak ada projek dalam To-Do.
                    </div>
                </template>
            </div>
        </div>

        <!-- COLUMN 2: ON-GOING -->
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200/80 space-y-3 min-h-[500px] flex flex-col"
             @dragover.prevent="dragOverColumn = 'ongoing'"
             @dragleave="dragOverColumn = null"
             @drop.prevent="handleDropToColumn('ongoing')"
             :class="{'!border-blue-500 !bg-blue-50/60 ring-2 ring-blue-400 border-dashed': dragOverColumn === 'ongoing'}">
            
            <!-- Column Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">ON-GOING</h3>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded-full text-xs font-extrabold" x-text="getColumnEvents('ongoing').length"></span>
                </div>
                <a href="{{ route('admin.events.create') }}?status=acc" class="text-xs text-slate-400 hover:text-blue-600 font-semibold flex items-center gap-1" title="Tambah On-Going">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <!-- Event Cards List -->
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar max-h-[700px] pr-0.5">
                <template x-for="event in getColumnEvents('ongoing')" :key="event.id">
                    <div draggable="true"
                         @dragstart="handleDragStart($event, event)"
                         @dragend="draggedEvent = null; dragOverColumn = null;"
                         @click="openDetailModal(event)"
                         :class="isKunjungan(event) 
                            ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' 
                            : 'bg-white text-slate-800 border-slate-200 hover:border-blue-400'"
                         class="p-4 rounded-xl border shadow-xs hover:shadow-md transition-all cursor-grab active:cursor-grabbing space-y-2 relative group">
                        
                        <!-- Card Header Tags -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex flex-wrap gap-1">
                                <template x-if="isKunjungan(event)">
                                    <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                </template>
                                <template x-for="cat in event.categories">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                </template>
                            </div>
                            <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800 shrink-0">ACC</span>
                        </div>

                        <!-- Card Title -->
                        <h4 class="font-bold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                        <!-- Date & Time -->
                        <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                            <i class="far fa-calendar-alt text-[11px]"></i>
                            <span x-text="formatDate(event.event_date)"></span>
                            <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                        </div>

                        <!-- Card Footer: Members & Quick Edit -->
                        <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-100'">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                    <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                </template>
                                <template x-if="event.members.length > 3">
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                </template>
                            </div>

                            <a :href="'/admin/events/' + event.id + '/edit'" @click.stop class="text-xs p-1 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="getColumnEvents('ongoing').length === 0">
                    <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-200 rounded-xl">
                        Tidak ada projek dalam On-Going.
                    </div>
                </template>
            </div>
        </div>

        <!-- COLUMN 3: COMPLETE -->
        <div class="bg-slate-100/70 rounded-2xl p-4 border border-slate-200/80 space-y-3 min-h-[500px] flex flex-col"
             @dragover.prevent="dragOverColumn = 'complete'"
             @dragleave="dragOverColumn = null"
             @drop.prevent="handleDropToColumn('complete')"
             :class="{'!border-green-500 !bg-green-50/60 ring-2 ring-green-400 border-dashed': dragOverColumn === 'complete'}">
            
            <!-- Column Header -->
            <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <h3 class="font-bold text-slate-800 text-sm tracking-wide uppercase">COMPLETE</h3>
                    <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs font-extrabold" x-text="getColumnEvents('complete').length"></span>
                </div>
                <a href="{{ route('admin.events.create') }}?status=selesai" class="text-xs text-slate-400 hover:text-green-600 font-semibold flex items-center gap-1" title="Tambah Complete">
                    <i class="fas fa-plus"></i>
                </a>
            </div>

            <!-- Event Cards List -->
            <div class="space-y-3 flex-1 overflow-y-auto custom-scrollbar max-h-[700px] pr-0.5">
                <template x-for="event in getColumnEvents('complete')" :key="event.id">
                    <div draggable="true"
                         @dragstart="handleDragStart($event, event)"
                         @dragend="draggedEvent = null; dragOverColumn = null;"
                         @click="openDetailModal(event)"
                         :class="isKunjungan(event) 
                            ? 'bg-indigo-600 text-white border-indigo-700 hover:bg-indigo-700' 
                            : 'bg-white text-slate-800 border-slate-200 hover:border-blue-400'"
                         class="p-4 rounded-xl border shadow-xs hover:shadow-md transition-all cursor-grab active:cursor-grabbing space-y-2 relative group opacity-90">
                        
                        <!-- Card Header Tags -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="flex flex-wrap gap-1">
                                <template x-if="isKunjungan(event)">
                                    <span class="bg-indigo-800 text-indigo-100 font-bold px-1.5 py-0.5 rounded text-[9px] uppercase"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                                </template>
                                <template x-for="cat in event.categories">
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                </template>
                            </div>
                            <span class="text-[9px] uppercase font-extrabold px-1.5 py-0.5 rounded bg-green-100 text-green-800 shrink-0">SELESAI</span>
                        </div>

                        <!-- Card Title -->
                        <h4 class="font-bold text-sm leading-snug line-clamp-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-900'" x-text="event.title"></h4>

                        <!-- Date & Time -->
                        <div class="flex items-center gap-2 text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'">
                            <i class="far fa-calendar-alt text-[11px]"></i>
                            <span x-text="formatDate(event.event_date)"></span>
                            <span x-text="event.formatted_time ? '• ' + event.formatted_time : ''"></span>
                        </div>

                        <!-- Card Footer: Members & Quick Edit -->
                        <div class="pt-2 border-t flex items-center justify-between gap-2" :class="isKunjungan(event) ? 'border-indigo-500/50' : 'border-slate-100'">
                            <div class="flex flex-wrap gap-1">
                                <template x-for="m in event.members.slice(0, 3)" :key="m.id">
                                    <span class="text-[9px] px-1.5 py-0.5 rounded text-white font-medium" :style="'background-color: ' + m.color" x-text="m.full_name.split(' ')[0]"></span>
                                </template>
                                <template x-if="event.members.length > 3">
                                    <span class="text-[9px] px-1 py-0.5 rounded bg-slate-200 text-slate-700 font-bold" x-text="'+' + (event.members.length - 3)"></span>
                                </template>
                            </div>

                            <a :href="'/admin/events/' + event.id + '/edit'" @click.stop class="text-xs p-1 text-slate-400 hover:text-blue-600 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        </div>
                    </div>
                </template>

                <template x-if="getColumnEvents('complete').length === 0">
                    <div class="text-center py-8 text-slate-400 text-xs italic border-2 border-dashed border-slate-200 rounded-xl">
                        Tidak ada projek dalam Complete.
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    <div x-show="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="selectedEvent = null"></div>
        <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-200 max-w-lg w-full p-6 relative z-10">
            <button @click="selectedEvent = null" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>

            <div class="flex items-center gap-2 mb-2" x-show="isKunjungan(selectedEvent)">
                <span class="px-2.5 py-0.5 bg-indigo-600 text-white font-bold text-xs rounded-full uppercase"><i class="fas fa-bus mr-1"></i> Kegiatan Kunjungan</span>
            </div>

            <h3 class="text-2xl font-bold text-slate-800 mb-4 pr-6" x-text="selectedEvent?.title"></h3>

            <div class="space-y-3 text-sm mb-6">
                <div class="flex items-center gap-3">
                    <span class="w-24 text-slate-400 font-medium">Tanggal:</span>
                    <span class="font-semibold text-slate-800" x-text="formatDate(selectedEvent?.event_date)"></span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-24 text-slate-400 font-medium">Waktu:</span>
                    <span class="font-semibold text-slate-800" x-text="selectedEvent?.formatted_time || 'Seharian'"></span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="w-24 text-slate-400 font-medium">Status:</span>
                    <span class="px-2.5 py-0.5 rounded text-xs font-bold uppercase tracking-wider"
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
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded" x-text="m.full_name + (m.pivot && m.pivot.role ? ' (' + m.pivot.role + ')' : '')"></span>
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
                            <a :href="l.url" target="_blank" class="px-2.5 py-1 bg-blue-50 border border-blue-200 text-blue-600 hover:bg-blue-100 text-xs font-semibold rounded flex items-center gap-1">
                                <i class="fas fa-external-link-alt text-[10px]"></i> <span x-text="l.title || 'Link'"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                <button type="button" @click="selectedEvent = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">Tutup</button>
                <a :href="'/admin/events/' + selectedEvent?.id + '/edit'" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-edit"></i> Edit Projek
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('projectKanban', () => ({
            events: {!! json_encode($events) !!},
            searchQuery: '',
            draggedEvent: null,
            dragOverColumn: null,
            selectedEvent: null,
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

            getColumnEvents(colName) {
                return this.events.filter(event => {
                    // Filter Search
                    if (this.searchQuery.trim() !== '') {
                        let q = this.searchQuery.toLowerCase();
                        if (!event.title.toLowerCase().includes(q)) return false;
                    }

                    // Status Mapping
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
            },

            handleDragStart(e, event) {
                this.draggedEvent = event;
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', event.id);
                }
            },

            async handleDropToColumn(columnName) {
                let eventToMove = this.draggedEvent;
                this.dragOverColumn = null;
                this.draggedEvent = null;

                if (!eventToMove) return;

                // Determine target status
                let targetStatus = 'terjadwal';
                if (columnName === 'ongoing') targetStatus = 'acc';
                else if (columnName === 'complete') targetStatus = 'selesai';
                else if (columnName === 'todo') targetStatus = ['ide', 'ditunda', 'ditolak'].includes(eventToMove.status) ? eventToMove.status : 'terjadwal';

                if (eventToMove.status === targetStatus) return;

                let oldStatus = eventToMove.status;
                // Optimistic UI Update
                eventToMove.status = targetStatus;

                try {
                    let response = await fetch(`/admin/projects/${eventToMove.id}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            column: columnName
                        })
                    });

                    let data = await response.json();
                    if (response.ok && data.success) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Status projek berhasil diperbarui!',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    } else {
                        // Rollback
                        eventToMove.status = oldStatus;
                        alert(data.message || 'Gagal memperbarui status projek.');
                    }
                } catch (err) {
                    // Rollback
                    eventToMove.status = oldStatus;
                    alert('Terjadi kesalahan koneksi saat memperbarui status projek.');
                }
            }
        }));
    });
</script>
@endpush
