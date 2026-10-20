@extends('layouts.app')
@section('title', 'Kalender Kegiatan - Tim Kreatif PMB UAD')
@section('content')
<x-public-blur-lock>
<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-8" x-data="calendarData()">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Kalender Kegiatan</h1>
        
        <div class="flex items-center gap-4 w-full sm:w-auto justify-between sm:justify-end">
            <div class="flex bg-white rounded-lg shadow-sm p-1 border border-slate-200">
                <button @click="changeMonth(-1)" class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded transition-colors"><i class="fas fa-chevron-left"></i></button>
                <div class="px-4 py-1 font-semibold text-slate-800 min-w-[120px] text-center" x-text="monthName + ' ' + currentYear"></div>
                <button @click="changeMonth(1)" class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded transition-colors"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button @click="resetToToday()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors shadow-sm font-medium">Hari Ini</button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden text-slate-800">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
            <template x-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']">
                <div class="py-3 text-center text-xs md:text-sm font-semibold text-slate-500" x-text="day"></div>
            </template>
        </div>
        
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 auto-rows-[minmax(55px,auto)] md:auto-rows-[minmax(120px,auto)] bg-slate-200 gap-[1px]">
            <template x-for="(day, index) in calendarDays" :key="index">
                <div class="min-h-[55px] md:min-h-[120px] bg-white p-1.5 md:p-2 relative group transition-all cursor-pointer md:cursor-default hover:bg-slate-50/80" 
                     @click="selectDay(day)"
                     :class="{
                         'opacity-40 bg-slate-50/50': !day.isCurrentMonth, 
                         'bg-blue-50/50': day.isToday,
                         'ring-2 ring-blue-600 ring-inset bg-blue-50/20 z-10': selectedDate && selectedDate.date === day.date && day.isCurrentMonth
                     }">
                    <!-- Date Number -->
                    <div class="flex justify-between items-start mb-1">
                        <span class="text-xs md:text-sm font-semibold w-6 h-6 md:w-7 md:h-7 flex items-center justify-center rounded-full" 
                              :class="day.isToday ? 'bg-blue-600 text-white shadow-sm' : (selectedDate && selectedDate.date === day.date && day.isCurrentMonth ? 'text-blue-600 font-bold' : 'text-slate-400')" x-text="day.date"></span>
                    </div>

                    <!-- Events (Desktop Only) -->
                    <div class="hidden md:block space-y-1.5 overflow-y-auto max-h-[250px] pr-1 custom-scrollbar">
                        <template x-for="event in day.events" :key="event.id">
                            <div @click.stop="openModal(event)" 
                                 :class="isKunjungan(event) 
                                    ? 'bg-indigo-600 border border-indigo-700 text-white p-2 rounded-md cursor-pointer hover:bg-indigo-700 hover:shadow-md transition-all shadow-sm group/event' 
                                    : 'bg-white border border-slate-200 p-2 rounded-md cursor-pointer hover:border-blue-400 hover:shadow transition-all shadow-sm group/event'">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="text-sm font-semibold truncate pr-2" :class="isKunjungan(event) ? 'text-white' : 'text-slate-800'" x-text="event.title"></div>
                                    <div class="text-xs font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'" x-text="event.formatted_time"></div>
                                </div>
                                
                                <!-- Status / Kunjungan Badge -->
                                <div class="flex items-center gap-1.5 mb-2">
                                    <template x-if="isKunjungan(event)">
                                        <span class="bg-indigo-800 text-indigo-100 text-[9px] font-bold px-1.5 py-0.5 rounded tracking-wider uppercase"><i class="fas fa-bus mr-1"></i> KUNJUNGAN</span>
                                    </template>
                                    <template x-if="!isKunjungan(event)">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full" 
                                                :class="{
                                                    'bg-green-500': event.status === 'selesai',
                                                    'bg-yellow-500': event.status === 'ide',
                                                    'bg-red-500': event.status === 'ditunda',
                                                    'bg-blue-500': event.status === 'acc',
                                                    'bg-purple-500': event.status === 'terjadwal',
                                                    'bg-rose-500': event.status === 'ditolak'
                                                }"></span>
                                            <span class="text-[10px] uppercase font-medium tracking-wider text-slate-400" x-text="event.status"></span>
                                        </div>
                                    </template>
                                </div>

                                <!-- Tags -->
                                <div class="flex flex-wrap gap-1">
                                    <!-- Category Tags -->
                                    <template x-for="cat in event.categories">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                                    </template>
                                    
                                    <!-- Member Tags -->
                                    <template x-for="member in event.members">
                                        <span class="text-[10px] px-1.5 py-0.5 rounded text-white" :style="'background-color: ' + member.color" x-text="member.full_name.split(' ')[0]"></span>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Events Indicator Dots (Mobile Only) -->
                    <div class="flex md:hidden justify-center flex-wrap gap-0.5 mt-0.5 overflow-hidden max-h-5">
                        <template x-for="event in day.events" :key="event.id">
                            <span class="w-2 h-2 rounded-full border border-white" 
                                  :class="isKunjungan(event) ? 'bg-indigo-600 ring-1 ring-indigo-400' : {
                                      'bg-green-500': event.status === 'selesai',
                                      'bg-yellow-500': event.status === 'ide',
                                      'bg-red-500': event.status === 'ditunda',
                                      'bg-blue-500': event.status === 'acc',
                                      'bg-purple-500': event.status === 'terjadwal',
                                      'bg-rose-500': event.status === 'ditolak'
                                  }"></span>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Agenda Event List (Mobile Only) -->
    <div class="md:hidden mt-4 bg-white rounded-xl shadow-sm border border-slate-200 p-4" x-show="selectedDate">
        <h3 class="text-base font-bold text-slate-800 mb-3 flex items-center gap-2">
            <i class="far fa-calendar-alt text-blue-600"></i>
            <span>Kegiatan:</span>
            <span class="text-blue-600" x-text="selectedDate ? selectedDate.date + ' ' + monthName + ' ' + currentYear : ''"></span>
        </h3>
        
        <div class="space-y-2.5">
            <template x-for="event in (selectedDate ? selectedDate.events : [])" :key="event.id">
                <div @click="openModal(event)" 
                     :class="isKunjungan(event) 
                        ? 'bg-indigo-600 text-white border-indigo-700 p-3 rounded-lg cursor-pointer transition-all flex justify-between items-center shadow-sm' 
                        : 'border border-slate-100 bg-slate-50 hover:bg-blue-50/20 active:bg-blue-50/40 p-3 rounded-lg cursor-pointer transition-all flex justify-between items-center shadow-xs'">
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-sm truncate" :class="isKunjungan(event) ? 'text-white' : 'text-slate-800'" x-text="event.title"></div>
                        <div class="flex items-center gap-2 mt-1">
                            <template x-if="isKunjungan(event)">
                                <span class="bg-indigo-800 text-indigo-100 text-[9px] font-bold px-1.5 py-0.5 rounded tracking-wider uppercase"><i class="fas fa-bus mr-1"></i> KUNJUNGAN</span>
                            </template>
                            <template x-if="!isKunjungan(event)">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2.5 h-2.5 rounded-full" 
                                        :class="{
                                            'bg-green-500': event.status === 'selesai',
                                            'bg-yellow-500': event.status === 'ide',
                                            'bg-red-500': event.status === 'ditunda',
                                            'bg-blue-500': event.status === 'acc',
                                            'bg-purple-500': event.status === 'terjadwal',
                                            'bg-rose-500': event.status === 'ditolak'
                                        }"></span>
                                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider" x-text="event.status"></span>
                                </div>
                            </template>
                            <span :class="isKunjungan(event) ? 'text-indigo-300' : 'text-slate-300'" class="text-xs">|</span>
                            <span class="text-[10px] font-mono" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-500'" x-text="event.formatted_time || 'Seharian'"></span>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-xs ml-2" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-300'"></i>
                </div>
            </template>
            
            <template x-if="!selectedDate || selectedDate.events.length === 0">
                <div class="text-center py-6 text-slate-400 italic text-sm">
                    <i class="fas fa-calendar-times text-xl mb-1.5 block"></i>
                    Tidak ada kegiatan pada tanggal ini.
                </div>
            </template>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div x-show="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;" x-cloak>
        <div x-show="selectedEvent" x-transition.opacity class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="closeModal()"></div>
        
        <div x-show="selectedEvent" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="bg-white text-slate-700 rounded-xl shadow-2xl border border-slate-200 w-full max-w-lg relative z-10 overflow-hidden">
             
            <div class="p-6">
                <button @click="closeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-800 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>

                <div class="flex items-center gap-2 mb-2" x-show="isKunjungan(selectedEvent)">
                    <span class="px-2.5 py-0.5 bg-indigo-600 text-white font-bold text-xs rounded-full uppercase tracking-wider"><i class="fas fa-bus mr-1"></i> Kegiatan Kunjungan</span>
                </div>

                <h2 class="text-2xl font-bold text-slate-800 mb-6 pr-8" x-text="selectedEvent?.title"></h2>

                <div class="space-y-4">
                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2"><i class="far fa-calendar-alt w-4"></i> Tanggal</div>
                        <div class="text-slate-700 text-sm font-medium" x-text="formatDate(selectedEvent?.event_date)"></div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2"><i class="far fa-clock w-4"></i> Waktu</div>
                        <div class="text-slate-700 text-sm font-medium" x-text="selectedEvent?.formatted_time || '-'"></div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2"><i class="fas fa-dot-circle w-4"></i> Status</div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" 
                                :class="{
                                    'bg-green-500': selectedEvent?.status === 'selesai',
                                    'bg-yellow-500': selectedEvent?.status === 'ide',
                                    'bg-red-500': selectedEvent?.status === 'ditunda',
                                    'bg-blue-500': selectedEvent?.status === 'acc',
                                    'bg-purple-500': selectedEvent?.status === 'terjadwal',
                                    'bg-rose-500': selectedEvent?.status === 'ditolak'
                                }"></span>
                            <span class="text-sm font-medium capitalize" x-text="selectedEvent?.status"></span>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2 mt-0.5"><i class="fas fa-tags w-4"></i> Tags</div>
                        <div class="flex flex-wrap gap-1.5 flex-1">
                            <template x-for="cat in selectedEvent?.categories">
                                <span class="text-[11px] px-2 py-1 rounded text-white" :style="'background-color: ' + cat.color" x-text="cat.name"></span>
                            </template>
                            <template x-if="!selectedEvent?.categories || selectedEvent?.categories.length === 0">
                                <span class="text-sm text-slate-500 italic">-</span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2 mt-0.5"><i class="fas fa-users w-4"></i> Anggota</div>
                        <div class="flex flex-col gap-1.5 flex-1">
                            <template x-for="member in selectedEvent?.members">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full" :style="'background-color: ' + member.color"></span>
                                    <span class="text-sm text-slate-700 font-medium">
                                        <span x-text="member.full_name"></span>
                                        <template x-if="member.pivot && member.pivot.role">
                                            <span class="text-slate-500 font-normal" x-text="' - ' + member.pivot.role"></span>
                                        </template>
                                    </span>
                                </div>
                            </template>
                            <template x-if="!selectedEvent?.members || selectedEvent?.members.length === 0">
                                <span class="text-sm text-slate-500 italic">Belum ada petugas</span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2"><i class="fas fa-align-left w-4"></i> Deskripsi</div>
                        <div class="text-slate-700 text-sm flex-1 whitespace-pre-wrap" x-text="selectedEvent?.description || '-'"></div>
                    </div>

                    <!-- Multi-link Section -->
                    <div class="flex items-start gap-4 pt-2 border-t border-slate-100">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2 mt-1.5"><i class="fas fa-link w-4"></i> Tautan</div>
                        <div class="flex-1">
                            <div class="flex flex-wrap gap-2 mb-2">
                                <template x-for="(l, idx) in getEventLinks(selectedEvent)" :key="idx">
                                    <a :href="l.url" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 border border-blue-200 hover:bg-blue-100 text-blue-700 text-xs font-semibold rounded-lg transition-colors shadow-xs">
                                        <i class="fas fa-external-link-alt"></i> <span x-text="l.title || 'Tautan'"></span>
                                    </a>
                                </template>
                                <template x-if="getEventLinks(selectedEvent).length === 0">
                                    <span class="text-sm text-slate-400 italic">Belum ada tautan</span>
                                </template>
                            </div>
                            <button @click="openAddLinkModal()" class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-md transition-colors mt-1">
                                <i class="fas fa-plus-circle text-blue-600"></i> Edit / Tambah Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Password Confirmation Modal for Public Link Edit -->
    <div x-show="showAddLinkModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddLinkModal = false"></div>
        <div class="bg-white text-slate-800 rounded-2xl shadow-2xl border border-slate-100 max-w-md w-full p-6 relative z-10">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-100">
                <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                    <i class="fas fa-link text-blue-600"></i> Tambah / Edit Link Kegiatan
                </h3>
                <button @click="showAddLinkModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>

            <template x-if="linkError">
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-xs flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span x-text="linkError"></span>
                </div>
            </template>

            <form @submit.prevent="submitLink()">
                <div class="space-y-3 mb-5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Judul / Label Tautan</label>
                        <input type="text" x-model="linkTitle" placeholder="Contoh: Dokumentasi Drive, Reels IG, TikTok" class="w-full p-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">URL Tautan</label>
                        <input type="url" x-model="linkUrl" placeholder="https://..." class="w-full p-2.5 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                    </div>
                    <div class="bg-amber-50/70 p-3 rounded-lg border border-amber-200/70">
                        <label class="block text-xs font-bold text-amber-900 mb-1"><i class="fas fa-key text-amber-600 mr-1"></i> Kode Konfirmasi (PIN Admin)</label>
                        <input type="password" x-model="linkPin" placeholder="Masukkan PIN Kode Admin" class="w-full p-2.5 text-sm border border-amber-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white font-mono tracking-widest font-bold text-slate-800" required>
                        <p class="text-[11px] text-amber-700 mt-1">Hanya pengguna yang memiliki kode PIN valid dari Admin yang dapat menyimpan perubahan tautan.</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showAddLinkModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium">Batal</button>
                    <button type="submit" :disabled="isSubmittingLink" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm disabled:opacity-50">
                        <template x-if="isSubmittingLink"><i class="fas fa-spinner fa-spin"></i></template>
                        <span>Simpan Tautan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
</x-public-blur-lock>

@push('styles')
<style>
    /* Custom Scrollbar for Event Container inside cells */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background-color: #4b5563; /* slate-600 */
        border-radius: 20px;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('calendarData', () => ({
            currentDate: new Date(),
            currentYear: {{ isset($activePeriod) && is_numeric($activePeriod->name) ? (int)$activePeriod->name : 'new Date().getFullYear()' }},
            currentMonth: new Date().getMonth(),
            events: [],
            calendarDays: [],
            isLoading: false,
            selectedEvent: null,
            selectedDate: null,
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            
            get monthName() {
                return this.monthNames[this.currentMonth];
            },

            getDateStr(val) {
                if (!val) return '';
                if (typeof val === 'string') {
                    return val.split('T')[0];
                }
                if (val instanceof Date) {
                    const y = val.getFullYear();
                    const m = String(val.getMonth() + 1).padStart(2, '0');
                    const d = String(val.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                }
                return '';
            },

            init() {
                this.fetchEvents();
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
                this.fetchEvents();
            },

            resetToToday() {
                let today = new Date();
                this.currentMonth = today.getMonth();
                this.currentYear = today.getFullYear();
                this.fetchEvents();
            },

            async fetchEvents() {
                this.isLoading = true;
                try {
                    // API request to get events for current year and month
                    let response = await fetch(`/api/events/${this.currentYear}/${this.currentMonth + 1}`);
                    let data = await response.json();
                    this.events = data;
                    this.generateCalendar();
                } catch (error) {
                    console.error("Error fetching events:", error);
                } finally {
                    this.isLoading = false;
                }
            },

            generateCalendar() {
                this.calendarDays = [];
                let firstDay = new Date(this.currentYear, this.currentMonth, 1).getDay(); // 0 = Sunday
                let daysInMonth = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                let prevMonthDays = new Date(this.currentYear, this.currentMonth, 0).getDate();
                
                let today = new Date();
                let isCurrentMonthYear = today.getMonth() === this.currentMonth && today.getFullYear() === this.currentYear;
                let todayDate = today.getDate();

                // Previous month padding
                for (let i = firstDay - 1; i >= 0; i--) {
                    this.calendarDays.push({
                        date: prevMonthDays - i,
                        isCurrentMonth: false,
                        isToday: false,
                        events: []
                    });
                }

                // Current month
                for (let i = 1; i <= daysInMonth; i++) {
                    const monthStr = String(this.currentMonth + 1).padStart(2, '0');
                    const dayStr = String(i).padStart(2, '0');
                    const fullDate = `${this.currentYear}-${monthStr}-${dayStr}`;

                    let dayEvents = this.events.filter(e => {
                        return this.getDateStr(e.event_date) === fullDate;
                    });
                    
                    this.calendarDays.push({
                        date: i,
                        fullDate: fullDate,
                        isCurrentMonth: true,
                        isToday: isCurrentMonthYear && i === todayDate,
                        events: dayEvents
                    });
                }

                // Next month padding (to complete 42 cells / 6 rows)
                let remainingCells = 42 - this.calendarDays.length;
                for (let i = 1; i <= remainingCells; i++) {
                    this.calendarDays.push({
                        date: i,
                        isCurrentMonth: false,
                        isToday: false,
                        events: []
                    });
                }

                // Auto select today or first day
                let todayDay = this.calendarDays.find(d => d.isToday && d.isCurrentMonth);
                if (todayDay) {
                    this.selectedDate = todayDay;
                } else {
                    let firstDayOfMonth = this.calendarDays.find(d => d.date === 1 && d.isCurrentMonth);
                    this.selectedDate = firstDayOfMonth || null;
                }
            },

            selectDay(day) {
                if (!day.isCurrentMonth) return;
                this.selectedDate = day;
            },

            openModal(event) {
                this.selectedEvent = event;
                document.body.style.overflow = 'hidden';
            },

            closeModal() {
                this.selectedEvent = null;
                document.body.style.overflow = '';
            },

            formatDate(dateString) {
                if (!dateString) return '';
                let str = this.getDateStr(dateString);
                let parts = str.split('-');
                if (parts.length === 3) {
                    let y = parseInt(parts[0], 10);
                    let m = parseInt(parts[1], 10) - 1;
                    let d = parseInt(parts[2], 10);
                    return `${d} ${this.monthNames[m]} ${y}`;
                }
                return str;
            },

            showAddLinkModal: false,
            linkTitle: '',
            linkUrl: '',
            linkPin: '',
            linkError: '',
            isSubmittingLink: false,

            isKunjungan(event) {
                if (!event) return false;
                let titleMatch = event.title && event.title.toLowerCase().includes('kunjungan');
                let catMatch = event.categories && event.categories.some(c => c.name && c.name.toLowerCase().includes('kunjungan'));
                return titleMatch || catMatch;
            },

            getEventLinks(event) {
                if (!event) return [];
                if (Array.isArray(event.links) && event.links.length > 0) {
                    return event.links;
                }
                if (event.link) {
                    return [{ title: 'Tautan Utama', url: event.link }];
                }
                return [];
            },

            openAddLinkModal() {
                this.linkTitle = '';
                this.linkUrl = '';
                this.linkPin = '';
                this.linkError = '';
                this.showAddLinkModal = true;
            },

            async submitLink() {
                if (!this.selectedEvent) return;
                this.isSubmittingLink = true;
                this.linkError = '';

                try {
                    let response = await fetch(`/api/events/${this.selectedEvent.id}/update-link`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            title: this.linkTitle,
                            url: this.linkUrl,
                            pin: this.linkPin
                        })
                    });

                    let data = await response.json();
                    if (response.ok && data.success) {
                        this.selectedEvent.links = data.links;
                        this.showAddLinkModal = false;
                        alert(data.message || 'Tautan berhasil ditambahkan!');
                    } else {
                        this.linkError = data.message || 'Kode Konfirmasi (PIN) salah!';
                    }
                } catch (e) {
                    this.linkError = 'Gagal menyimpan tautan. Silakan periksa koneksi internet Anda.';
                } finally {
                    this.isSubmittingLink = false;
                }
            }
        }));
    });
</script>
@endpush
@endsection
