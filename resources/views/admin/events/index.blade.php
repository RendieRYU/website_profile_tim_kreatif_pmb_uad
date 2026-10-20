@extends('layouts.admin')

@section('main_class', 'p-2.5 md:p-3.5')

@section('content')
<div x-data="adminCalendar()" class="space-y-2">
    <!-- Header Title & Switch View Controls -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800">Manajemen Kegiatan</h1>
            <p class="text-slate-500 text-xs mt-0.5">Kelola agenda dan jadwal kegiatan [Periode {{ $activePeriod->name }}]</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Mode Switcher (Kalender / Spreadsheet Tabel) -->
            <div class="flex bg-slate-100 p-0.5 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">
                <button @click="viewMode = 'calendar'" 
                        :class="viewMode === 'calendar' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'hover:text-slate-900'"
                        class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                    <i class="fas fa-calendar-alt text-xs"></i> Kalender
                </button>
                <button @click="viewMode = 'table'" 
                        :class="viewMode === 'table' ? 'bg-white text-blue-600 shadow-xs font-bold' : 'hover:text-slate-900'"
                        class="px-3 py-1 rounded-lg transition-all flex items-center gap-1.5">
                    <i class="fas fa-table text-xs"></i> Spreadsheet Tabel
                </button>
            </div>

            <!-- Action Buttons -->
            <a href="{{ route('admin.events.export') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors flex items-center justify-center gap-1.5 text-xs font-bold shadow-xs">
                <i class="fas fa-file-excel"></i> Ekspor Excel
            </a>
            <a href="{{ route('admin.events.create') }}" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors flex items-center justify-center gap-1.5 text-xs font-bold shadow-xs">
                <i class="fas fa-plus"></i> Tambah Kegiatan
            </a>
        </div>
    </div>

    <!-- CALENDAR VIEW (FITS EXACT 1 SCREEN HEIGHT) -->
    <div x-show="viewMode === 'calendar'" x-cloak class="space-y-1.5">
        <!-- Calendar Nav Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white px-3 py-1.5 rounded-xl shadow-xs border border-slate-100">
            <div class="flex items-center gap-2">
                <div class="flex items-center bg-slate-50 border border-slate-200 rounded-lg p-0.5">
                    <button @click="changeMonth(-1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-white hover:shadow-xs rounded-md transition-all"><i class="fas fa-chevron-left text-[10px]"></i></button>
                    <span class="px-3 font-bold text-slate-800 text-xs min-w-[130px] text-center" x-text="monthName + ' ' + currentYear"></span>
                    <button @click="changeMonth(1)" class="w-7 h-7 flex items-center justify-center text-slate-600 hover:bg-white hover:shadow-xs rounded-md transition-all"><i class="fas fa-chevron-right text-[10px]"></i></button>
                </div>
                <button @click="resetToToday()" class="px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors">Hari Ini</button>
            </div>

            <!-- Legend Badges -->
            <div class="flex flex-wrap items-center gap-2 text-[11px] font-medium text-slate-600">
                <span class="text-[10px] text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md font-semibold"><i class="fas fa-arrows-alt text-[9px] mr-1"></i> Drag & drop untuk reschedule</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-indigo-600"></span> Kunjungan</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500"></span> Selesai</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500"></span> ACC</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-purple-500"></span> Terjadwal</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-yellow-500"></span> Ide</span>
            </div>
        </div>

        <!-- Interactive Calendar Grid (Zero Page Scroll) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden flex flex-col h-[calc(100vh-160px)]">
            <!-- Day of Week Headers -->
            <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center py-1.5 text-[11px] font-extrabold uppercase tracking-wider text-slate-500 shrink-0">
                <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
            </div>

            <!-- Month Days Cells (Exact 6 Equal Rows) -->
            <div class="grid grid-cols-7 grid-rows-6 flex-1 divide-x divide-y divide-slate-100 min-h-0 overflow-hidden">
                <template x-for="(day, index) in calendarDays" :key="index">
                    <div @dragover.prevent="handleDragOver($event, day)"
                         @dragleave="handleDragLeave($event, day)"
                         @drop.prevent="handleDrop($event, day)"
                         class="h-full min-h-0 p-1 transition-all flex flex-col justify-between group relative overflow-hidden"
                         :class="{
                             'bg-slate-50/50 text-slate-300': !day.isCurrentMonth,
                             'bg-white text-slate-800': day.isCurrentMonth,
                             'bg-blue-50/30': day.isToday,
                             '!bg-blue-100/70 border-2 border-dashed !border-blue-500 scale-[0.99]': dragOverDayDate === day.fullDate
                         }">
                        
                        <!-- Cell Header: Date Number & Quick Add -->
                        <div class="flex justify-between items-center mb-0.5 shrink-0"
                             :class="{ 'pointer-events-none': draggedEvent !== null }">
                            <span class="text-[11px] font-bold w-5 h-5 flex items-center justify-center rounded-full"
                                  :class="day.isToday ? 'bg-blue-600 text-white shadow-xs font-extrabold' : ''"
                                  x-text="day.date"></span>
                            
                            <template x-if="day.isCurrentMonth">
                                <a :href="'{{ route('admin.events.create') }}?date=' + day.fullDate" 
                                   class="opacity-0 group-hover:opacity-100 text-[9px] bg-blue-50 text-blue-600 font-bold px-1.5 py-0.5 rounded hover:bg-blue-100 transition-opacity" 
                                   title="Tambah kegiatan pada tanggal ini">+ Tambah</a>
                            </template>
                        </div>

                        <!-- Event Cards in Cell (Draggable) -->
                        <div class="space-y-0.5 overflow-y-auto custom-scrollbar flex-1 min-h-0 pr-0.5"
                             :class="{ 'pointer-events-none': draggedEvent !== null }">
                            <template x-for="event in day.events" :key="event.id">
                                <div draggable="true"
                                     @dragstart="handleDragStart($event, event)"
                                     @dragend="handleDragEnd($event)"
                                     @click="selectedEvent = event"
                                     class="p-1 rounded text-left text-[10px] leading-tight font-semibold cursor-grab active:cursor-grabbing hover:shadow-md transition-all border block relative group/card"
                                     :class="isKunjungan(event) 
                                         ? 'bg-indigo-600 text-white border-indigo-700 shadow-2xs' 
                                         : 'bg-white border-slate-200 text-slate-800 hover:border-blue-300 shadow-2xs'">
                                     
                                    <div class="flex items-center gap-1">
                                        <i class="fas fa-grip-vertical text-[8px] opacity-40 group-hover/card:opacity-90"></i>
                                        <span class="truncate flex-1" x-text="event.title"></span>
                                    </div>

                                    <div class="flex items-center justify-between text-[9px] mt-0.5">
                                        <template x-if="isKunjungan(event)">
                                            <span class="bg-indigo-800 text-indigo-100 font-bold text-[7px] px-1 rounded uppercase">Kunjungan</span>
                                        </template>
                                        <template x-if="!isKunjungan(event)">
                                            <span class="font-bold uppercase tracking-wider text-[8px]"
                                                  :class="{
                                                      'text-green-600': event.status === 'selesai',
                                                      'text-yellow-600': event.status === 'ide',
                                                      'text-red-600': event.status === 'ditunda',
                                                      'text-blue-600': event.status === 'acc',
                                                      'text-purple-600': event.status === 'terjadwal',
                                                      'text-rose-600': event.status === 'ditolak'
                                                  }" x-text="event.status"></span>
                                        </template>
                                        <span class="font-mono text-[8px]" :class="isKunjungan(event) ? 'text-indigo-200' : 'text-slate-400'" x-text="event.formatted_time || ''"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- SPREADSHEET TABLE VIEW (INTERACTIVE) -->
    <div x-show="viewMode === 'table'" x-cloak class="space-y-3">
        <!-- Spreadsheet Top Actions -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-3.5 rounded-2xl shadow-xs border border-slate-100">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-excel text-emerald-600 text-lg"></i>
                <div>
                    <h3 class="font-extrabold text-sm text-slate-800">Spreadsheet Kegiatan Interaktif</h3>
                    <p class="text-[11px] text-slate-400">Klik sel mana saja untuk mengedit langsung. Data tersimpan otomatis ke database & Kalender.</p>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto flex-wrap">
                <!-- Status Filter -->
                <div class="relative" x-data="{ showFilter: false }">
                    <button @click="showFilter = !showFilter" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-200">
                        <i class="fas fa-filter text-[10px]"></i>
                        <span x-text="filterStatus ? filterStatus.toUpperCase() : 'Semua Status'"></span>
                        <i class="fas fa-chevron-down text-[9px] text-slate-400"></i>
                    </button>
                    <div x-show="showFilter" @click.away="showFilter = false" class="absolute z-[100] mt-1 right-0 w-44 bg-white border border-slate-200 shadow-2xl rounded-2xl p-2 overflow-hidden" style="display:none;">
                        <button @click="filterStatus = ''; showFilter = false" class="w-full text-left px-3 py-1.5 text-xs rounded-lg hover:bg-slate-50 font-semibold" :class="filterStatus === '' ? 'bg-blue-50 text-blue-700' : 'text-slate-700'">Semua Status</button>
                        <template x-for="s in ['ide','ditunda','terjadwal','acc','selesai','ditolak']" :key="s">
                            <button @click="filterStatus = s; showFilter = false" class="w-full text-left px-3 py-1.5 text-xs rounded-lg hover:bg-slate-50 font-semibold uppercase" :class="filterStatus === s ? 'bg-blue-50 text-blue-700' : 'text-slate-700'" x-text="s"></button>
                        </template>
                    </div>
                </div>

                <!-- Export Excel Button inside Spreadsheet Toolbar -->
                <a href="{{ route('admin.events.export') }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-file-excel"></i> Ekspor Excel
                </a>

                <button @click="openAddColumnModal()" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition-all shadow-xs flex items-center gap-1.5">
                    <i class="fas fa-columns"></i> + Tambah Kolom Kustom
                </button>
            </div>
        </div>

        <!-- Spreadsheet Grid Container -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-auto max-h-[calc(100vh-260px)]">
            <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                <thead class="sticky top-0 z-20">
                    <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 font-extrabold uppercase tracking-wider select-none">
                        <th class="p-3 border-r border-slate-200 w-12 text-center bg-slate-100">No</th>
                        
                        <!-- DYNAMIC COLUMNS WITH DRAG & DROP -->
                        <template x-for="(col, index) in tableColumns" :key="col.id">
                            <th draggable="true"
                                @dragstart="handleColDragStart($event, index)"
                                @dragover.prevent="handleColDragOver($event, index)"
                                @dragleave="handleColDragLeave($event)"
                                @drop.prevent="handleColDrop($event, index)"
                                @dragend="draggedColIndex = null; dragOverColIndex = null"
                                :class="[col.width, dragOverColIndex === index ? 'bg-blue-200' : (col.type === 'custom' ? 'bg-blue-50/50' : 'bg-slate-100')]"
                                class="p-3 border-r border-slate-200 cursor-move transition-colors relative group">
                                <div class="flex items-center justify-between gap-1">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <i class="fas fa-grip-vertical text-[10px] text-slate-400 opacity-50 group-hover:opacity-100"></i>
                                        <span x-text="col.name" :class="col.type === 'custom' ? 'text-blue-900' : ''"></span>
                                    </div>
                                    <div class="flex items-center gap-0.5">
                                        <!-- Sort button for sortable columns -->
                                        <template x-if="['title','event_date','event_time','status','category_id'].includes(col.id)">
                                            <button type="button" draggable="false" @click.stop.prevent="toggleSort(col.id)" class="text-slate-400 hover:text-blue-600 p-0.5 rounded transition-colors cursor-pointer" title="Urutkan">
                                                <i class="fas text-[10px]" :class="sortColumn === col.id ? (sortDirection === 'asc' ? 'fa-sort-up text-blue-600' : 'fa-sort-down text-blue-600') : 'fa-sort'"></i>
                                            </button>
                                        </template>
                                        <!-- Delete button for custom columns -->
                                        <template x-if="col.type === 'custom'">
                                            <button type="button" draggable="false" @click.stop.prevent="deleteColumn(col, index)" class="relative z-30 text-red-500 hover:text-red-700 hover:bg-red-50 p-1 rounded-md transition-all cursor-pointer inline-flex items-center justify-center ml-0.5" title="Hapus Kolom Ini">
                                                <i class="fas fa-times text-xs pointer-events-none"></i>
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </th>
                        </template>

                        <th class="p-3 text-center w-24 bg-slate-100">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                    <template x-for="(event, idx) in displayedEvents" :key="event.id">
                        <tr draggable="true"
                            @dragstart="handleRowDragStart($event, idx)"
                            @dragover.prevent="handleRowDragOver($event, idx)"
                            @dragleave="dragOverRowIndex = null"
                            @drop.prevent="handleRowDrop($event, idx)"
                            @dragend="draggedRowIndex = null; dragOverRowIndex = null"
                            class="transition-colors group cursor-move"
                            :class="{
                                'hover:bg-blue-50/20': dragOverRowIndex !== idx,
                                'border-t-2 border-t-blue-500 bg-blue-50/40': dragOverRowIndex === idx
                            }">
                            <!-- No -->
                            <td class="p-2 border-r border-slate-200 text-center text-slate-400 font-mono">
                                <div class="flex items-center justify-center gap-1">
                                    <i class="fas fa-grip-vertical text-[9px] text-slate-300 opacity-0 group-hover:opacity-100"></i>
                                    <span x-text="idx + 1"></span>
                                </div>
                            </td>

                            <!-- Dynamic Columns -->
                            <template x-for="col in tableColumns" :key="col.id">
                                <td class="p-1 border-r border-slate-200" :class="col.type === 'custom' ? 'bg-blue-50/10' : ''">
                                    <template x-if="col.id === 'title'">
                                        <input type="text" x-model="event.title" @blur="saveInlineCell(event, 'title', event.title)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-bold text-slate-900 bg-transparent hover:bg-white focus:bg-white" placeholder="Nama kegiatan...">
                                    </template>
                                    <template x-if="col.id === 'event_date'">
                                        <input type="date" :value="getDateStr(event.event_date)" @change="event.event_date = $event.target.value; saveInlineCell(event, 'event_date', $event.target.value)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-medium text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                    </template>
                                    <template x-if="col.id === 'event_time'">
                                        <input type="time" :value="event.formatted_time || ''" @change="event.formatted_time = $event.target.value; saveInlineCell(event, 'event_time', $event.target.value)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                    </template>
                                    <template x-if="col.id === 'status'">
                                        <select x-model="event.status" @change="saveInlineCell(event, 'status', event.status)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-bold uppercase bg-transparent hover:bg-white focus:bg-white"
                                                :class="{
                                                    'text-green-700': event.status === 'selesai',
                                                    'text-yellow-700': event.status === 'ide',
                                                    'text-red-700': event.status === 'ditunda',
                                                    'text-blue-700': event.status === 'acc',
                                                    'text-purple-700': event.status === 'terjadwal',
                                                    'text-rose-700': event.status === 'ditolak'
                                                }">
                                            <option value="ide">Ide</option>
                                            <option value="acc">ACC</option>
                                            <option value="terjadwal">Terjadwal</option>
                                            <option value="ditunda">Ditunda</option>
                                            <option value="selesai">Selesai</option>
                                            <option value="ditolak">Ditolak</option>
                                        </select>
                                    </template>
                                     <template x-if="col.id === 'category_id'">
                                        <div x-data="{ open: false, selected: event.categories ? event.categories.map(c => c.id.toString()) : [] }" class="relative">
                                            <div @click="open = !open" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-medium bg-transparent hover:bg-white focus:bg-white cursor-pointer truncate flex items-center justify-between">
                                                <span x-text="selected.length ? selected.length + ' Kategori' : 'Tanpa Kategori'"></span>
                                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                                            </div>
                                            <div x-show="open" @click.away="open = false; saveInlineCell(event, 'category_id', selected)" class="absolute z-[90] mt-1 w-56 bg-white border border-slate-200 shadow-2xl rounded-2xl p-2.5 max-h-60 overflow-y-auto left-0" style="display: none;">
                                                <template x-for="cat in categories" :key="cat.id">
                                                    <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                                        <input type="checkbox" :value="cat.id.toString()" x-model="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="inline-block w-2 h-2 rounded-full" :style="'background-color: ' + cat.color"></span>
                                                        <span class="text-xs text-slate-700" x-text="cat.name"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="col.id === 'member_id'">
                                        <div x-data="{ open: false, selected: event.members ? event.members.map(m => m.id.toString()) : [] }" class="relative">
                                            <div @click="open = !open" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-medium bg-transparent hover:bg-white focus:bg-white cursor-pointer truncate flex items-center justify-between">
                                                <span x-text="selected.length ? selected.length + ' Petugas' : 'Pilih Petugas...'"></span>
                                                <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                                            </div>
                                            <div x-show="open" @click.away="open = false; saveInlineCell(event, 'member_id', selected)" class="absolute z-[90] mt-1 w-56 bg-white border border-slate-200 shadow-2xl rounded-2xl p-2.5 max-h-60 overflow-y-auto left-0" style="display: none;">
                                                <template x-for="m in members" :key="m.id">
                                                    <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                                        <input type="checkbox" :value="m.id.toString()" x-model="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                        <span class="text-xs text-slate-700" x-text="m.full_name"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                    <!-- Custom column: text -->
                                    <template x-if="col.type === 'custom' && (!col.col_type || col.col_type === 'text')">
                                        <input type="text" :value="(event.custom_fields && event.custom_fields[col.id]) ? event.custom_fields[col.id] : ''" @blur="saveCustomFieldCell(event, col.id, $event.target.value)" placeholder="Isi..." class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                    </template>
                                    <!-- Custom column: dropdown -->
                                    <template x-if="col.type === 'custom' && col.col_type === 'dropdown'">
                                        <select :value="(event.custom_fields && event.custom_fields[col.id]) ? event.custom_fields[col.id] : ''" @change="saveCustomFieldCell(event, col.id, $event.target.value)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs bg-transparent hover:bg-white focus:bg-white">
                                            <option value="">Pilih...</option>
                                            <template x-for="opt in (col.options || '').split(',').map(o => o.trim()).filter(o => o)" :key="opt">
                                                <option :value="opt" x-text="opt" :selected="event.custom_fields && event.custom_fields[col.id] === opt"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <!-- Custom column: checklist -->
                                    <template x-if="col.type === 'custom' && col.col_type === 'checklist'">
                                        <div class="flex items-center justify-center">
                                            <input type="checkbox" :checked="event.custom_fields && event.custom_fields[col.id] === 'ya'" @change="saveCustomFieldCell(event, col.id, $event.target.checked ? 'ya' : 'tidak')" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        </div>
                                    </template>
                                    <!-- Custom column: date -->
                                    <template x-if="col.type === 'custom' && col.col_type === 'date'">
                                        <input type="date" :value="(event.custom_fields && event.custom_fields[col.id]) ? event.custom_fields[col.id] : ''" @change="saveCustomFieldCell(event, col.id, $event.target.value)" class="w-full px-2 py-1.5 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                    </template>
                                </td>
                            </template>

                            <!-- Actions -->
                            <td class="p-2 text-center space-x-1">
                                <a :href="'/admin/events/' + event.id + '/edit'" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-md" title="Edit Lengkap (Notion Page)"><i class="fas fa-edit"></i></a>
                                <button @click="deleteEvent(event)" class="p-1.5 text-red-500 hover:bg-red-50 rounded-md" title="Hapus Baris"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    </template>

                    <!-- INLINE ADD NEW ROW (+ TAMBAH BARIS BARU) -->
                    <tr class="bg-emerald-50/40 border-t-2 border-emerald-300">
                        <td class="p-3 text-center text-emerald-600 font-bold"><i class="fas fa-plus"></i></td>
                        <!-- Dynamic Columns for New Row -->
                        <template x-for="col in tableColumns" :key="col.id">
                            <td class="p-1 border-r border-slate-200" :class="col.type === 'custom' ? 'bg-emerald-50/20' : ''">
                                <template x-if="col.id === 'title'">
                                    <input type="text" x-model="newRow.title" @keydown.enter="submitNewRow()" placeholder="+ Ketik judul..." class="w-full px-2.5 py-2 border border-emerald-300 focus:ring-2 focus:ring-emerald-500 rounded-lg text-xs font-bold text-slate-900 bg-white shadow-2xs">
                                </template>
                                <template x-if="col.id === 'event_date'">
                                    <input type="date" x-model="newRow.event_date" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                </template>
                                <template x-if="col.id === 'event_time'">
                                    <input type="time" x-model="newRow.event_time" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                </template>
                                <template x-if="col.id === 'status'">
                                    <select x-model="newRow.status" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs font-bold bg-white uppercase">
                                        <option value="terjadwal">Terjadwal</option>
                                        <option value="ide">Ide</option>
                                        <option value="acc">ACC</option>
                                        <option value="ditunda">Ditunda</option>
                                        <option value="selesai">Selesai</option>
                                        <option value="ditolak">Ditolak</option>
                                    </select>
                                </template>
                                 <template x-if="col.id === 'category_id'">
                                    <div x-data="{ open: false }" class="relative">
                                        <div @click="open = !open" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white cursor-pointer truncate flex items-center justify-between">
                                            <span x-text="newRow.category_id.length ? newRow.category_id.length + ' Kategori' : 'Tanpa Kategori'"></span>
                                            <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                                        </div>
                                        <div x-show="open" @click.away="open = false" class="absolute z-[100] bottom-full mb-2 w-56 bg-white border border-slate-200 shadow-2xl rounded-2xl p-2.5 max-h-60 overflow-y-auto left-0" style="display: none;">
                                            <template x-for="cat in categories" :key="cat.id">
                                                <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                                    <input type="checkbox" :value="cat.id.toString()" x-model="newRow.category_id" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="inline-block w-2 h-2 rounded-full" :style="'background-color: ' + cat.color"></span>
                                                    <span class="text-xs text-slate-700" x-text="cat.name"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="col.id === 'member_id'">
                                    <div x-data="{ open: false }" class="relative">
                                        <div @click="open = !open" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white cursor-pointer truncate flex items-center justify-between">
                                            <span x-text="newRow.member_id.length ? newRow.member_id.length + ' Petugas' : 'Pilih Petugas...'"></span>
                                            <i class="fas fa-chevron-down text-[10px] text-slate-400"></i>
                                        </div>
                                        <div x-show="open" @click.away="open = false" class="absolute z-[100] bottom-full mb-2 w-56 bg-white border border-slate-200 shadow-2xl rounded-2xl p-2.5 max-h-60 overflow-y-auto left-0" style="display: none;">
                                            <template x-for="m in members" :key="m.id">
                                                <label class="flex items-center gap-2 p-1.5 hover:bg-slate-50 rounded-lg cursor-pointer">
                                                    <input type="checkbox" :value="m.id.toString()" x-model="newRow.member_id" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                    <span class="text-xs text-slate-700" x-text="m.full_name"></span>
                                                </label>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                                <!-- New row: custom text -->
                                <template x-if="col.type === 'custom' && (!col.col_type || col.col_type === 'text')">
                                    <input type="text" x-model="newRow.custom_fields[col.id]" placeholder="Isi..." class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                </template>
                                <!-- New row: custom dropdown -->
                                <template x-if="col.type === 'custom' && col.col_type === 'dropdown'">
                                    <select x-model="newRow.custom_fields[col.id]" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                        <option value="">Pilih...</option>
                                        <template x-for="opt in (col.options || '').split(',').map(o => o.trim()).filter(o => o)" :key="opt">
                                            <option :value="opt" x-text="opt"></option>
                                        </template>
                                    </select>
                                </template>
                                <!-- New row: custom checklist -->
                                <template x-if="col.type === 'custom' && col.col_type === 'checklist'">
                                    <div class="flex items-center justify-center py-1">
                                        <input type="checkbox" @change="newRow.custom_fields[col.id] = $event.target.checked ? 'ya' : 'tidak'" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </div>
                                </template>
                                <!-- New row: custom date -->
                                <template x-if="col.type === 'custom' && col.col_type === 'date'">
                                    <input type="date" x-model="newRow.custom_fields[col.id]" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs bg-white">
                                </template>
                            </td>
                        </template>

                        <td class="p-2 text-center">
                            <button @click="submitNewRow()" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-extrabold shadow-xs transition-all flex items-center gap-1 mx-auto">
                                <i class="fas fa-check"></i> Tambah
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL: TAMBAH KOLOM KUSTOM BARU -->
    <div x-show="showAddColModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddColModal = false"></div>
        <div class="bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 relative z-10">
            <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-100">
                <h3 class="font-extrabold text-base text-slate-900 flex items-center gap-2">
                    <i class="fas fa-columns text-blue-600"></i> Tambah Kolom Kustom
                </h3>
                <button @click="showAddColModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>

            <form @submit.prevent="submitNewColumn()">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Kolom</label>
                        <input type="text" x-model="newColName" placeholder="Contoh: Lokasi, PJ Konsumsi, Budget" class="w-full p-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold" required autofocus>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Data</label>
                        <div class="grid grid-cols-2 gap-2">
                            <template x-for="t in [{id:'text',icon:'fa-font',label:'Teks'},{id:'dropdown',icon:'fa-list',label:'Dropdown'},{id:'checklist',icon:'fa-check-square',label:'Checklist'},{id:'date',icon:'fa-calendar-day',label:'Tanggal'}]" :key="t.id">
                                <button type="button" @click="newColType = t.id" class="px-3 py-2.5 rounded-xl border text-xs font-bold flex items-center gap-2 transition-all" :class="newColType === t.id ? 'border-blue-500 bg-blue-50 text-blue-700 shadow-sm' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                                    <i class="fas text-sm" :class="t.icon"></i>
                                    <span x-text="t.label"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                    <div x-show="newColType === 'dropdown'" x-cloak>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Opsi Dropdown <span class="font-normal text-slate-400">(pisah dengan koma)</span></label>
                        <input type="text" x-model="newColOptions" placeholder="Contoh: Opsi A, Opsi B, Opsi C" class="w-full p-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showAddColModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">Simpan Kolom</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Event Detail Modal (Quick View) -->
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
                              'bg-green-100 text-green-800': selectedEvent?.status === 'selesai',
                              'bg-yellow-100 text-yellow-800': selectedEvent?.status === 'ide',
                              'bg-red-100 text-red-800': selectedEvent?.status === 'ditunda',
                              'bg-blue-100 text-blue-800': selectedEvent?.status === 'acc',
                              'bg-purple-100 text-purple-800': selectedEvent?.status === 'terjadwal',
                              'bg-rose-100 text-rose-800': selectedEvent?.status === 'ditolak'
                          }" x-text="selectedEvent?.status"></span>
                </div>
                <div class="flex items-start gap-3">
                    <span class="w-24 text-slate-400 font-medium mt-0.5">Petugas:</span>
                    <div class="flex-1 flex flex-wrap gap-1">
                        <template x-for="m in selectedEvent?.members" :key="m.id">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 text-xs font-semibold rounded" x-text="m.full_name + (m.pivot && m.pivot.role ? ' (' + m.pivot.role + ')' : '')"></span>
                        </template>
                    </div>
                </div>

                <!-- Custom Fields in Detail Modal -->
                <template x-for="col in customColumns" :key="col.id">
                    <div class="flex items-start gap-3" x-show="selectedEvent && selectedEvent.custom_fields && selectedEvent.custom_fields[col.id]">
                        <div class="w-24 text-slate-400 text-xs flex items-center gap-2 mt-0.5"><i class="fas fa-sticky-note w-4 text-center"></i> <span x-text="col.name"></span></div>
                        <span class="font-semibold text-slate-800" x-text="selectedEvent && selectedEvent.custom_fields ? selectedEvent.custom_fields[col.id] : ''"></span>
                    </div>
                </template>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" @click="deleteEvent(selectedEvent)" class="px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                    <i class="fas fa-trash"></i> Hapus
                </button>

                <div class="flex items-center gap-2">
                    <button type="button" @click="selectedEvent = null" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Tutup</button>
                    <a :href="'/admin/events/' + selectedEvent?.id + '/edit'" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5">
                        <i class="fas fa-edit"></i> Edit Lengkap
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@php
    $periodYear = isset($activePeriod) && is_numeric($activePeriod->name) ? (int)$activePeriod->name : (int)date('Y');
    $pStart = \Carbon\Carbon::createFromDate($periodYear - 1, 10, 1)->startOfDay();
    $pEnd = \Carbon\Carbon::createFromDate($periodYear, 9, 30)->endOfDay();
    $now = \Carbon\Carbon::now();
    if ($now->between($pStart, $pEnd)) {
        $initYear = (int)$now->year;
        $initMonth = (int)$now->month - 1;
    } else {
        $initYear = (int)$periodYear - 1;
        $initMonth = 9; // Oktober
    }
@endphp

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminCalendar', () => ({
            viewMode: 'calendar',
            currentYear: {{ $initYear }},
            currentMonth: {{ $initMonth }},
            events: {!! json_encode($allEvents) !!},
            categories: {!! json_encode($categories) !!},
            members: {!! json_encode($members) !!},
            customColumns: {!! json_encode($customColumns) !!},
            tableColumns: {!! isset($tableColumns) ? json_encode($tableColumns) : '[]' !!},
            
            calendarDays: [],
            selectedEvent: null,
            draggedEvent: null,
            dragOverDayDate: null,
            showAddColModal: false,
            newColName: '',
            newColType: 'text',
            newColOptions: '',
            
            // Sort & Filter
            sortColumn: null,
            sortDirection: 'asc',
            filterStatus: '',
            
            // Column drag
            draggedColIndex: null,
            dragOverColIndex: null,
            
            // Row drag
            draggedRowIndex: null,
            dragOverRowIndex: null,
            
            newRow: {
                title: '',
                event_date: '',
                event_time: '',
                status: 'terjadwal',
                category_id: [],
                member_id: [],
                custom_fields: {}
            },

            monthNames: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],

            getTodayStr() {
                const d = new Date();
                const y = d.getFullYear();
                const m = String(d.getMonth() + 1).padStart(2, '0');
                const date = String(d.getDate()).padStart(2, '0');
                return `${y}-${m}-${date}`;
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
                    return `${y}-${m}-${date}`;
                }
                return '';
            },

            get monthName() {
                return this.monthNames[this.currentMonth];
            },

            get displayedEvents() {
                let result = [...this.events];
                
                // Filter by status
                if (this.filterStatus) {
                    result = result.filter(e => e.status === this.filterStatus);
                }
                
                // Sort
                if (this.sortColumn) {
                    const col = this.sortColumn;
                    const dir = this.sortDirection === 'asc' ? 1 : -1;
                    const statusOrder = { ide: 0, ditunda: 1, terjadwal: 2, acc: 3, selesai: 4, ditolak: 5 };
                    
                    result.sort((a, b) => {
                        let valA, valB;
                        if (col === 'title') {
                            valA = (a.title || '').toLowerCase();
                            valB = (b.title || '').toLowerCase();
                            return valA.localeCompare(valB) * dir;
                        } else if (col === 'event_date') {
                            valA = a.event_date || '';
                            valB = b.event_date || '';
                            return valA.localeCompare(valB) * dir;
                        } else if (col === 'event_time') {
                            valA = a.formatted_time || '';
                            valB = b.formatted_time || '';
                            return valA.localeCompare(valB) * dir;
                        } else if (col === 'status') {
                            valA = statusOrder[a.status] ?? 99;
                            valB = statusOrder[b.status] ?? 99;
                            return (valA - valB) * dir;
                        } else if (col === 'category_id') {
                            valA = (a.categories && a.categories[0]) ? a.categories[0].name : '';
                            valB = (b.categories && b.categories[0]) ? b.categories[0].name : '';
                            return valA.localeCompare(valB) * dir;
                        }
                        return 0;
                    });
                }
                
                return result;
            },

            init() {
                this.newRow.event_date = this.getTodayStr();
                this.generateCalendar();
            },

            // SORT
            toggleSort(colId) {
                if (this.sortColumn === colId) {
                    if (this.sortDirection === 'asc') {
                        this.sortDirection = 'desc';
                    } else {
                        this.sortColumn = null;
                        this.sortDirection = 'asc';
                    }
                } else {
                    this.sortColumn = colId;
                    this.sortDirection = 'asc';
                }
            },

            // ROW DRAG & DROP
            handleRowDragStart(e, index) {
                this.draggedRowIndex = index;
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/row', index.toString());
                }
            },
            handleRowDragOver(e, index) {
                if (this.draggedRowIndex !== null && this.draggedRowIndex !== index) {
                    this.dragOverRowIndex = index;
                }
            },
            handleRowDrop(e, dropIndex) {
                if (this.draggedRowIndex === null || this.draggedRowIndex === dropIndex) return;
                const item = this.events.splice(this.draggedRowIndex, 1)[0];
                this.events.splice(dropIndex, 0, item);
                this.draggedRowIndex = null;
                this.dragOverRowIndex = null;
                // Clear sort when manually reordering
                this.sortColumn = null;
                this.sortDirection = 'asc';
            },

            // SPREADSHEET COLUMN DRAG & DROP
            handleColDragStart(e, index) {
                this.draggedColIndex = index;
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                }
            },
            handleColDragOver(e, index) {
                if (this.draggedColIndex !== null && this.draggedColIndex !== index) {
                    this.dragOverColIndex = index;
                }
            },
            handleColDragLeave(e) {
                this.dragOverColIndex = null;
            },
            async handleColDrop(e, dropIndex) {
                if (this.draggedColIndex === null || this.draggedColIndex === dropIndex) return;
                
                // Reorder array
                const item = this.tableColumns.splice(this.draggedColIndex, 1)[0];
                this.tableColumns.splice(dropIndex, 0, item);
                
                this.draggedColIndex = null;
                this.dragOverColIndex = null;

                // Save to backend
                let newOrder = this.tableColumns.map(c => c.id);
                try {
                    await fetch('/admin/events/columns/order', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ columns: newOrder })
                    });
                } catch(err) {
                    console.error("Failed to save column order", err);
                }
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

            // DRAG & DROP CALENDAR
            handleDragStart(e, event) {
                this.draggedEvent = event;
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', event.id.toString());
                }
            },

            handleDragEnd(e) {
                this.draggedEvent = null;
                this.dragOverDayDate = null;
            },

            handleDragOver(e, day) {
                if (day.isCurrentMonth && (this.draggedEvent || (e.dataTransfer && e.dataTransfer.types.includes('text/plain')))) {
                    if (e.dataTransfer) {
                        e.dataTransfer.dropEffect = 'move';
                    }
                    this.dragOverDayDate = day.fullDate;
                }
            },

            handleDragLeave(e, day) {
                if (!e.currentTarget.contains(e.relatedTarget)) {
                    if (this.dragOverDayDate === day.fullDate) {
                        this.dragOverDayDate = null;
                    }
                }
            },

            async handleDrop(e, day) {
                e.preventDefault();
                let eventToMove = this.draggedEvent;
                if (!eventToMove && e.dataTransfer) {
                    let eventId = e.dataTransfer.getData('text/plain');
                    if (eventId) {
                        eventToMove = this.events.find(ev => String(ev.id) === String(eventId));
                    }
                }
                this.dragOverDayDate = null;
                this.draggedEvent = null;

                if (!eventToMove || !day.fullDate) return;

                let currentDateStr = this.getDateStr(eventToMove.event_date);
                if (currentDateStr === day.fullDate) return;

                let previousDate = eventToMove.event_date;
                eventToMove.event_date = day.fullDate;
                this.generateCalendar();

                try {
                    let response = await fetch(`/admin/events/${eventToMove.id}/move`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ event_date: day.fullDate })
                    });

                    let data = await response.json();
                    if (response.ok && data.success) {
                        if (data.event) {
                            let idx = this.events.findIndex(e => e.id === eventToMove.id);
                            if (idx !== -1) {
                                this.events[idx] = data.event;
                            }
                            this.generateCalendar();
                        }
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Jadwal kegiatan berhasil dipindahkan!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        eventToMove.event_date = previousDate;
                        this.generateCalendar();
                        alert(data.message || 'Gagal memindahkan jadwal.');
                    }
                } catch (err) {
                    eventToMove.event_date = previousDate;
                    this.generateCalendar();
                    alert('Terjadi kesalahan koneksi saat memindahkan jadwal.');
                }
            },

            // SPREADSHEET INLINE EDITING
            async saveInlineCell(event, field, value) {
                try {
                    let res = await fetch(`/admin/events/${event.id}/inline-update`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ field: field, value: value })
                    });
                    let data = await res.json();
                    if (res.ok && data.success) {
                        let index = this.events.findIndex(e => e.id === event.id);
                        if (index !== -1) {
                            this.events[index] = data.event;
                        }
                        this.generateCalendar();
                    }
                } catch (e) {}
            },

            async saveCustomFieldCell(event, columnId, value) {
                if (!event.custom_fields) event.custom_fields = {};
                event.custom_fields[columnId] = value;

                try {
                    await fetch(`/admin/events/${event.id}/inline-update`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            field: 'custom_field',
                            column_id: columnId,
                            value: value
                        })
                    });
                } catch (e) {}
            },

            // TAMBAH BARIS BARU (INLINE QUICK STORE)
            async submitNewRow() {
                if (!this.newRow.title.trim()) {
                    alert('Mohon isi judul kegiatan terlebih dahulu.');
                    return;
                }

                try {
                    let res = await fetch('/admin/events/quick-store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(this.newRow)
                    });

                    let data = await res.json();
                    if (res.ok && data.success) {
                        this.events.unshift(data.event);
                        this.generateCalendar();
                        
                        // Reset new row
                        this.newRow = {
                            title: '',
                            event_date: this.getTodayStr(),
                            event_time: '',
                            status: 'terjadwal',
                            category_id: [],
                            member_id: [],
                            custom_fields: {}
                        };

                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Baris kegiatan berhasil ditambahkan!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    } else {
                        alert(data.message || 'Gagal menambahkan baris kegiatan.');
                    }
                } catch (e) {
                    alert('Terjadi kesalahan koneksi.');
                }
            },

            // TAMBAH KOLOM KUSTOM
            openAddColumnModal() {
                this.newColName = '';
                this.newColType = 'text';
                this.newColOptions = '';
                this.showAddColModal = true;
            },

            async submitNewColumn() {
                if (!this.newColName.trim()) return;

                try {
                    let res = await fetch('/admin/events/columns', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ name: this.newColName, type: this.newColType, options: this.newColOptions })
                    });

                    let data = await res.json();
                    if (res.ok && data.success) {
                        this.customColumns = data.columns;
                        if (data.new_column) {
                            this.tableColumns.push({
                                id: data.new_column.id,
                                name: data.new_column.name,
                                type: 'custom',
                                col_type: data.new_column.type || 'text',
                                options: data.new_column.options || '',
                                width: 'min-w-[160px]'
                            });
                            
                            // Save new order
                            let newOrder = this.tableColumns.map(c => c.id);
                            await fetch('/admin/events/columns/order', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                                },
                                body: JSON.stringify({ columns: newOrder })
                            });
                        }
                        this.showAddColModal = false;
                    }
                } catch (e) {
                    alert('Gagal menambahkan kolom kustom.');
                }
            },

            async deleteColumn(col, index) {
                let confirmed = false;
                if (typeof Swal !== 'undefined') {
                    let result = await Swal.fire({
                        title: 'Hapus Kolom?',
                        text: `Apakah Anda yakin ingin menghapus kolom kustom "${col.name}"? Data pada kolom ini akan hilang.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#64748b',
                        confirmButtonText: 'Ya, Hapus Kolom',
                        cancelButtonText: 'Batal'
                    });
                    confirmed = result.isConfirmed;
                } else {
                    confirmed = confirm(`Apakah Anda yakin ingin menghapus kolom kustom "${col.name}"?`);
                }

                if (!confirmed) return;

                try {
                    let res = await fetch(`/admin/events/columns/${col.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    let data = await res.json();
                    if (res.ok && data.success) {
                        this.customColumns = data.columns;
                        // Also remove from tableColumns
                        this.tableColumns = this.tableColumns.filter(c => c.id !== col.id);
                        
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Kolom berhasil dihapus!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    } else {
                        alert(data.message || 'Gagal menghapus kolom.');
                    }
                } catch (e) {
                    console.error('Delete column error:', e);
                    alert('Gagal menghapus kolom.');
                }
            },

            async deleteEvent(event) {
                if (!confirm(`Yakin ingin menghapus kegiatan "${event.title}"?`)) return;

                try {
                    let res = await fetch(`/admin/events/${event.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });

                    if (res.ok) {
                        this.events = this.events.filter(e => e.id !== event.id);
                        this.selectedEvent = null;
                        this.generateCalendar();
                    }
                } catch (e) {
                    alert('Gagal menghapus kegiatan.');
                }
            }
        }));
    });
</script>
@endpush
