@extends('layouts.admin')
@section('content')
<div x-data="adminCalendar()">
    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800">Manajemen Kegiatan</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola agenda dan jadwal kegiatan (Periode {{ $activePeriod->name }})</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- View Mode Switcher -->
            <div class="inline-flex bg-slate-200/80 p-1 rounded-lg border border-slate-200">
                <button @click="viewMode = 'calendar'" 
                        :class="viewMode === 'calendar' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-800 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fas fa-calendar-alt"></i> Kalender
                </button>
                <button @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-800 font-medium'"
                        class="px-3 py-1.5 text-xs rounded-md transition-all flex items-center gap-1.5">
                    <i class="fas fa-list"></i> Tabel
                </button>
            </div>

            <a href="{{ route('admin.events.export') }}" class="px-3.5 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors flex items-center justify-center gap-1.5 text-xs font-semibold shadow-sm">
                <i class="fas fa-file-excel"></i> Ekspor Excel
            </a>
            <a href="{{ route('admin.events.create') }}" class="px-3.5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-1.5 text-xs font-semibold shadow-sm">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    </div>

    <!-- CALENDAR VIEW -->
    <div x-show="viewMode === 'calendar'" x-cloak class="space-y-4">
        <!-- Calendar Header Bar -->
        <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-3">
                <button @click="changeMonth(-1)" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"><i class="fas fa-chevron-left text-xs"></i></button>
                <h2 class="text-lg font-bold text-slate-800 min-w-[160px] text-center" x-text="monthName + ' ' + currentYear"></h2>
                <button @click="changeMonth(1)" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-600 hover:bg-slate-100 transition-colors"><i class="fas fa-chevron-right text-xs"></i></button>
                <button @click="resetToToday()" class="px-3 py-1.5 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-colors">Hari Ini</button>
            </div>
            
            <div class="flex items-center gap-4 text-xs font-medium text-slate-600 flex-wrap justify-center">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-indigo-600"></span> Kunjungan</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-green-500"></span> Selesai</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-blue-500"></span> ACC</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-purple-500"></span> Terjadwal</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded bg-yellow-500"></span> Ide</span>
            </div>
        </div>

        <!-- Grid Kalender -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden text-slate-800">
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
                <template x-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']">
                    <div class="py-2.5 text-center text-xs md:text-sm font-semibold text-slate-500" x-text="day"></div>
                </template>
            </div>

            <div class="grid grid-cols-7 auto-rows-[minmax(80px,auto)] md:auto-rows-[minmax(120px,auto)] bg-slate-200 gap-[1px]">
                <template x-for="(day, index) in calendarDays" :key="index">
                    <div class="min-h-[80px] md:min-h-[120px] bg-white p-1.5 md:p-2 relative group transition-all flex flex-col justify-between"
                         :class="{'opacity-40 bg-slate-50/50': !day.isCurrentMonth, 'bg-blue-50/30': day.isToday}">
                        
                        <!-- Header Tanggal & Tombol Tambah -->
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full"
                                  :class="day.isToday ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-700'"
                                  x-text="day.date"></span>
                            
                            <template x-if="day.isCurrentMonth">
                                <a :href="'{{ route('admin.events.create') }}?date=' + day.fullDate" 
                                   class="text-[10px] bg-blue-50 hover:bg-blue-600 text-blue-600 hover:text-white px-1.5 py-0.5 rounded font-bold transition-all shadow-xs opacity-80 md:opacity-0 group-hover:opacity-100 flex items-center gap-1"
                                   title="Tambah kegiatan pada tanggal ini">
                                    <i class="fas fa-plus text-[9px]"></i> <span class="hidden md:inline">Tambah</span>
                                </a>
                            </template>
                        </div>

                        <!-- Daftar Event Sel Tanggal -->
                        <div class="space-y-1 overflow-y-auto max-h-[160px] pr-0.5 custom-scrollbar flex-1">
                            <template x-for="event in day.events" :key="event.id">
                                <div @click="openModal(event)"
                                     :class="isKunjungan(event) 
                                        ? 'bg-indigo-600 border border-indigo-700 text-white p-1.5 rounded cursor-pointer hover:bg-indigo-700 transition-all shadow-xs' 
                                        : 'bg-slate-50 hover:bg-blue-50/60 border border-slate-200 text-slate-800 p-1.5 rounded cursor-pointer transition-all shadow-xs'">
                                    <div class="flex justify-between items-center gap-1">
                                        <div class="text-xs font-semibold truncate leading-tight" :class="isKunjungan(event) ? 'text-white' : 'text-slate-800'" x-text="event.title"></div>
                                    </div>
                                    
                                    <div class="flex items-center justify-between mt-1 text-[10px]">
                                        <template x-if="isKunjungan(event)">
                                            <span class="bg-indigo-800 text-indigo-100 font-bold px-1 py-0.2 rounded text-[8px]">KUNJUNGAN</span>
                                        </template>
                                        <template x-if="!isKunjungan(event)">
                                            <span class="font-semibold uppercase tracking-wider text-[9px]"
                                                  :class="{
                                                      'text-green-600 font-bold': event.status === 'selesai',
                                                      'text-yellow-600 font-bold': event.status === 'ide',
                                                      'text-red-600 font-bold': event.status === 'ditunda',
                                                      'text-blue-600 font-bold': event.status === 'acc',
                                                      'text-purple-600 font-bold': event.status === 'terjadwal'
                                                  }" x-text="event.status"></span>
                                        </template>
                                        <span class="font-mono text-[9px]" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-400'" x-text="event.formatted_time || ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- TABLE VIEW -->
    <div x-show="viewMode === 'table'" x-cloak class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-xs uppercase font-semibold text-slate-500">
                        <th class="px-3 py-3 md:p-4">ID</th>
                        <th class="px-3 py-3 md:p-4">Judul Kegiatan</th>
                        <th class="px-3 py-3 md:p-4">Tanggal & Waktu</th>
                        <th class="px-3 py-3 md:p-4">Status</th>
                        <th class="px-3 py-3 md:p-4">Petugas</th>
                        <th class="px-3 py-3 md:p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($events as $event)
                    @php
                        $isKunjungan = str_contains(strtolower($event->title), 'kunjungan') || $event->categories->contains(fn($c) => str_contains(strtolower($c->name), 'kunjungan'));
                    @endphp
                    <tr class="hover:bg-slate-50 transition-colors {{ $isKunjungan ? 'bg-indigo-50/30' : '' }}">
                        <td class="px-3 py-3 md:p-4 text-slate-500">{{ $events->firstItem() + $loop->index }}</td>
                        <td class="px-3 py-3 md:p-4">
                            <div class="font-bold text-slate-800">{{ $event->title }}</div>
                            @if($isKunjungan)
                                <span class="bg-indigo-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded inline-block mt-0.5"><i class="fas fa-bus mr-1"></i> Kunjungan</span>
                            @endif
                        </td>
                        <td class="px-3 py-3 md:p-4 text-slate-600 text-xs">
                            <div><i class="far fa-calendar-alt mr-1"></i> {{ $event->event_date->format('d M Y') }}</div>
                            <div class="text-slate-400 font-mono"><i class="far fa-clock mr-1"></i> {{ $event->event_time ? $event->event_time->format('H:i') : '-' }}</div>
                        </td>
                        <td class="px-3 py-3 md:p-4">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider" 
                                  :class="{
                                      'bg-green-100 text-green-800': '{{ $event->status }}' === 'selesai',
                                      'bg-yellow-100 text-yellow-800': '{{ $event->status }}' === 'ide',
                                      'bg-red-100 text-red-800': '{{ $event->status }}' === 'ditunda',
                                      'bg-blue-100 text-blue-800': '{{ $event->status }}' === 'acc',
                                      'bg-purple-100 text-purple-800': '{{ $event->status }}' === 'terjadwal'
                                  }">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="px-3 py-3 md:p-4 text-slate-600 text-xs">
                            <span class="px-2 py-1 bg-slate-100 rounded-lg font-medium">{{ $event->members->count() }} Anggota</span>
                        </td>
                        <td class="px-3 py-3 md:p-4 text-right space-x-2 text-sm">
                            <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-500 hover:text-blue-700 inline-block p-1" title="Edit"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus kegiatan ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-500 hover:text-red-700 p-1" title="Hapus"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">Belum ada data kegiatan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($events->hasPages())
        <div class="p-4 border-t border-slate-100 bg-slate-50">
            {{ $events->links() }}
        </div>
        @endif
    </div>

    <!-- Event Detail Modal (Admin Quick View / Actions) -->
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
                    <span class="px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider" 
                          :class="{
                              'bg-green-100 text-green-800': selectedEvent?.status === 'selesai',
                              'bg-yellow-100 text-yellow-800': selectedEvent?.status === 'ide',
                              'bg-red-100 text-red-800': selectedEvent?.status === 'ditunda',
                              'bg-blue-100 text-blue-800': selectedEvent?.status === 'acc',
                              'bg-purple-100 text-purple-800': selectedEvent?.status === 'terjadwal'
                          }" x-text="selectedEvent?.status"></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-24 text-slate-400 font-medium mt-0.5">Petugas:</span>
                    <div class="flex-1 flex flex-wrap gap-1">
                        <template x-for="m in selectedEvent?.members" :key="m.id">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded" x-text="m.full_name + (m.pivot && m.pivot.role ? ' (' + m.pivot.role + ')' : '')"></span>
                        </template>
                        <template x-if="!selectedEvent?.members || selectedEvent?.members.length === 0">
                            <span class="text-slate-400 italic">Belum ada</span>
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

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <form :action="'/admin/events/' + selectedEvent?.id" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>

                <div class="flex items-center gap-2">
                    <button type="button" @click="selectedEvent = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold">Tutup</button>
                    <a :href="'/admin/events/' + selectedEvent?.id + '/edit'" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold shadow-xs flex items-center gap-1.5">
                        <i class="fas fa-edit"></i> Edit Kegiatan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminCalendar', () => ({
            viewMode: 'calendar',
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            events: {!! json_encode($allEvents) !!},
            calendarDays: [],
            selectedEvent: null,
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

            get monthName() {
                return this.monthNames[this.currentMonth];
            },

            init() {
                this.generateCalendar();
            },

            changeMonth(delta) {
                this.currentMonth += delta;
                if (this.currentMonth > 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else if (this.currentMonth < 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                }
                this.generateCalendar();
            },

            resetToToday() {
                let today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.generateCalendar();
            },

            generateCalendar() {
                this.calendarDays = [];
                let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay();
                let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                let prevMonthDays = new Date(this.currentYear, this.currentMonth, 0).getDate();
                
                let today = new Date();
                let isCurrentMonthYear = today.getMonth() === this.currentMonth && today.getFullYear() === this.currentYear;
                let todayDate = today.getDate();

                // Padding Prev Month
                for (let i = firstDay - 1; i >= 0; i--) {
                    let prevMonth = this.currentMonth === 0 ? 12 : this.currentMonth;
                    let prevYear = this.currentMonth === 0 ? this.currentYear - 1 : this.currentYear;
                    let d = prevMonthDays - i;
                    let fullDate = `${prevYear}-${String(prevMonth).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    this.calendarDays.push({
                        date: d,
                        fullDate: fullDate,
                        isCurrentMonth: false,
                        isToday: false,
                        events: []
                    });
                }

                // Current Month
                for (let i = 1; i <= daysInMonth; i++) {
                    let monthStr = String(this.currentMonth + 1).padStart(2, '0');
                    let dayStr = String(i).padStart(2, '0');
                    let fullDate = `${this.currentYear}-${monthStr}-${dayStr}`;

                    let dayEvents = this.events.filter(e => {
                        let ed = new Date(e.event_date);
                        return ed.getDate() === i && ed.getMonth() === this.currentMonth && ed.getFullYear() === this.currentYear;
                    });
                    
                    this.calendarDays.push({
                        date: i,
                        fullDate: fullDate,
                        isCurrentMonth: true,
                        isToday: isCurrentMonthYear && i === todayDate,
                        events: dayEvents
                    });
                }

                // Padding Next Month
                let remainingCells = 42 - this.calendarDays.length;
                for (let i = 1; i <= remainingCells; i++) {
                    let nextMonth = this.currentMonth === 11 ? 1 : this.currentMonth + 2;
                    let nextYear = this.currentMonth === 11 ? this.currentYear + 1 : this.currentYear;
                    let fullDate = `${nextYear}-${String(nextMonth).padStart(2, '0')}-${String(i).padStart(2, '0')}`;
                    this.calendarDays.push({
                        date: i,
                        fullDate: fullDate,
                        isCurrentMonth: false,
                        isToday: false,
                        events: []
                    });
                }
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

            openModal(event) {
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
