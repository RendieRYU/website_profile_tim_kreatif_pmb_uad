@extends('layouts.app')
@section('title', 'Kalender Kegiatan - Tim Kreatif PMB UAD')
@section('content')

<div class="w-full max-w-full px-4 sm:px-6 lg:px-8 py-8" x-data="calendarData()">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-slate-800">Kalender Kegiatan</h1>
        
        <div class="flex items-center gap-4">
            <div class="flex bg-white rounded-lg shadow-sm p-1 border border-slate-200">
                <button @click="changeMonth(-1)" class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded transition-colors"><i class="fas fa-chevron-left"></i></button>
                <div class="px-4 py-1 font-semibold text-slate-800" x-text="monthName + ' ' + currentYear"></div>
                <button @click="changeMonth(1)" class="px-3 py-1 text-slate-600 hover:bg-slate-100 rounded transition-colors"><i class="fas fa-chevron-right"></i></button>
            </div>
            <button @click="resetToToday()" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 rounded-lg hover:bg-slate-50 transition-colors shadow-sm font-medium">Hari Ini</button>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden text-slate-800">
        <!-- Calendar Header -->
        <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50">
            <template x-for="day in ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab']">
                <div class="py-3 text-center text-sm font-medium text-slate-400" x-text="day"></div>
            </template>
        </div>
        
        <!-- Calendar Grid -->
        <div class="grid grid-cols-7 auto-rows-[minmax(120px,auto)] bg-slate-200 gap-[1px]">
            <template x-for="(day, index) in calendarDays" :key="index">
                <div class="min-h-[120px] bg-white p-2 relative group transition-colors hover:bg-slate-50" :class="{'opacity-40 bg-slate-50': !day.isCurrentMonth, 'bg-blue-50/50': day.isToday}">
                    <!-- Date Number -->
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full" 
                              :class="day.isToday ? 'bg-blue-600 text-white' : 'text-slate-400'" x-text="day.date"></span>
                    </div>

                    <!-- Events -->
                    <div class="space-y-1.5 overflow-y-auto max-h-[250px] pr-1 custom-scrollbar">
                        <template x-for="event in day.events" :key="event.id">
                            <div @click="openModal(event)" class="bg-white border border-slate-200 p-2 rounded-md cursor-pointer hover:border-blue-400 hover:shadow transition-all shadow-sm group/event">
                                <div class="flex justify-between items-start mb-1">
                                    <div class="text-sm font-semibold text-slate-800 truncate pr-2" x-text="event.title"></div>
                                    <div class="text-xs text-slate-500 font-mono" x-text="event.formatted_time"></div>
                                </div>
                                
                                <!-- Status Indicator -->
                                <div class="flex items-center gap-1.5 mb-2">
                                    <span class="w-2 h-2 rounded-full" 
                                        :class="{
                                            'bg-green-500': event.status === 'selesai',
                                            'bg-yellow-500': event.status === 'ide',
                                            'bg-red-500': event.status === 'ditunda'
                                        }"></span>
                                    <span class="text-[10px] uppercase font-medium tracking-wider text-slate-400" x-text="event.status"></span>
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
                </div>
            </template>
        </div>
    </div>

    <!-- Event Detail Modal -->
    <div x-show="selectedEvent" class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-0" style="display: none;">
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
                                    'bg-red-500': selectedEvent?.status === 'ditunda'
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
                            <template x-for="member in selectedEvent?.members">
                                <span class="text-[11px] px-2 py-1 rounded text-white" :style="'background-color: ' + member.color" x-text="member.full_name"></span>
                            </template>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2"><i class="fas fa-align-left w-4"></i> Deskripsi</div>
                        <div class="text-slate-700 text-sm flex-1 whitespace-pre-wrap" x-text="selectedEvent?.description || '-'"></div>
                    </div>

                    <div class="flex items-start gap-4" x-show="selectedEvent?.link">
                        <div class="w-24 text-slate-400 text-sm flex items-center gap-2 mt-1.5"><i class="fas fa-link w-4"></i> Tautan</div>
                        <div>
                            <a :href="selectedEvent?.link" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-sm rounded transition-colors">
                                <i class="fas fa-external-link-alt"></i> Buka Tautan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            events: [],
            calendarDays: [],
            isLoading: false,
            selectedEvent: null,
            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            
            get monthName() {
                return this.monthNames[this.currentMonth];
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
                    let dayEvents = this.events.filter(e => {
                        let ed = new Date(e.event_date);
                        return ed.getDate() === i;
                    });
                    
                    this.calendarDays.push({
                        date: i,
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
                let d = new Date(dateString);
                return d.getDate() + ' ' + this.monthNames[d.getMonth()] + ' ' + d.getFullYear();
            }
        }));
    });
</script>
@endpush
@endsection
