@extends('layouts.admin')

@section('main_class', 'p-3 md:p-4')

@section('content')
<div x-data="instagramSpreadsheet()" class="space-y-3">
    <!-- Header Title & Action Toolbar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fab fa-instagram text-pink-600"></i> Laporan Postingan Instagram
            </h1>
            <p class="text-slate-500 text-xs mt-0.5">Spreadsheet metrik dan interaksi postingan Instagram [Periode {{ $activePeriod->name }}]</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <!-- Filter Month Dropdown -->
            <form action="{{ route('admin.instagram-posts.index') }}" method="GET" class="flex items-center gap-1.5">
                <select name="month" onchange="this.form.submit()" class="px-3 py-1.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 shadow-xs focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Bulan</option>
                    @foreach($allMonths as $value => $label)
                        <option value="{{ $value }}" {{ request('month') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            <!-- Export Excel -->
            <a href="{{ route('admin.instagram-posts.export', ['month' => request('month')]) }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl transition-colors flex items-center gap-1.5 text-xs font-bold shadow-xs">
                <i class="fas fa-file-excel"></i> Ekspor Excel
            </a>

            <!-- Tambah Kolom Kustom Modal Trigger -->
            <button @click="openAddColumnModal()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl transition-colors flex items-center gap-1.5 text-xs font-bold shadow-xs">
                <i class="fas fa-columns"></i> + Tambah Kolom Kustom
            </button>

            <!-- Toggle Quick Add Form -->
            <button @click="showQuickAdd = !showQuickAdd" class="px-3.5 py-1.5 bg-pink-600 hover:bg-pink-700 text-white rounded-xl transition-colors flex items-center gap-1.5 text-xs font-bold shadow-xs">
                <i class="fas fa-plus"></i> <span x-text="showQuickAdd ? 'Tutup Form' : 'Tambah Postingan'"></span>
            </button>
        </div>
    </div>

    <!-- Quick Add Top Bar (Collapsible) -->
    <div x-show="showQuickAdd" x-collapse class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200">
        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-1.5">
            <i class="fas fa-plus-circle text-pink-600"></i> Form Cepat Tambah Postingan Instagram
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 lg:grid-cols-10 gap-2 text-xs">
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Tanggal</label>
                <input type="date" x-model="newRow.tanggal_posting" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs font-medium">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Jenis</label>
                <select x-model="newRow.jenis_konten" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs font-semibold">
                    <option value="FEED">FEED</option>
                    <option value="REELS">REELS</option>
                    <option value="STORY">STORY</option>
                    <option value="OTHERS">OTHERS</option>
                </select>
            </div>
            <div class="col-span-2 sm:col-span-1">
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Pillar</label>
                <input type="text" x-model="newRow.pillar" placeholder="Pillar..." class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Likes</label>
                <input type="number" min="0" x-model.number="newRow.likes" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Comments</label>
                <input type="number" min="0" x-model.number="newRow.comments" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Shares</label>
                <input type="number" min="0" x-model.number="newRow.shares" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Saves</label>
                <input type="number" min="0" x-model.number="newRow.saves" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Reposts</label>
                <input type="number" min="0" x-model.number="newRow.reposts" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Reach</label>
                <input type="number" min="0" x-model.number="newRow.reach" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-500 mb-0.5">Followers +</label>
                <input type="number" min="0" x-model.number="newRow.new_followers" class="w-full px-2 py-1.5 border border-slate-200 rounded-lg text-xs text-center font-mono">
            </div>
        </div>
        <div class="mt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
            <input type="url" x-model="newRow.link_konten" placeholder="Link Konten Instagram (opsional)..." class="w-full px-2.5 py-1.5 border border-slate-200 rounded-lg text-xs">
            <button @click="submitQuickAdd()" :disabled="isSubmitting" class="py-1.5 px-6 bg-pink-600 hover:bg-pink-700 disabled:opacity-50 text-white rounded-lg font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1 shrink-0">
                <i class="fas fa-check"></i> Simpan Postingan
            </button>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 bg-white p-2.5 px-3.5 rounded-xl shadow-xs border border-slate-100 text-xs">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-bold text-slate-700 flex items-center gap-1"><i class="fas fa-table text-slate-400"></i> Spreadsheet Interaktif</span>
            <span class="text-slate-300">|</span>
            <!-- Filter Jenis Konten -->
            <div class="flex items-center gap-1">
                <span class="text-slate-500">Jenis:</span>
                <button @click="filterType = ''" :class="filterType === '' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'" class="px-2 py-0.5 rounded-md font-bold text-[10px] transition-all">SEMUA</button>
                <button @click="filterType = 'REELS'" :class="filterType === 'REELS' ? 'bg-pink-600 text-white' : 'bg-pink-50 text-pink-700 hover:bg-pink-100'" class="px-2 py-0.5 rounded-md font-bold text-[10px] transition-all">REELS</button>
                <button @click="filterType = 'FEED'" :class="filterType === 'FEED' ? 'bg-blue-600 text-white' : 'bg-blue-50 text-blue-700 hover:bg-blue-100'" class="px-2 py-0.5 rounded-md font-bold text-[10px] transition-all">FEED</button>
                <button @click="filterType = 'STORY'" :class="filterType === 'STORY' ? 'bg-amber-600 text-white' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'" class="px-2 py-0.5 rounded-md font-bold text-[10px] transition-all">STORY</button>
            </div>
            <span class="text-slate-300">|</span>
            <span class="text-[10px] text-slate-400 font-medium"><i class="fas fa-info-circle mr-0.5"></i> Kolom & baris bisa di-drag & drop</span>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <input type="text" x-model="searchQuery" placeholder="Cari tanggal / pillar..." class="w-full sm:w-48 px-2.5 py-1 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-blue-500">
            <span class="text-slate-400 shrink-0 font-medium" x-text="displayedPosts.length + ' baris'"></span>
        </div>
    </div>

    <!-- SPREADSHEET TABLE GRID -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-auto max-h-[calc(100vh-230px)]">
        <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
            <thead class="sticky top-0 z-20">
                <tr class="bg-slate-100 border-b border-slate-200 text-slate-700 font-extrabold uppercase tracking-wider select-none text-[11px]">
                    <th class="p-2.5 border-r border-slate-200 w-10 text-center bg-slate-100">No</th>
                    
                    <!-- Dynamic Headers with HTML5 Drag and Drop & Sorting -->
                    <template x-for="(col, index) in tableColumns" :key="col.id">
                        <th draggable="true"
                            @dragstart="handleColDragStart($event, index)"
                            @dragover.prevent="handleColDragOver($event, index)"
                            @dragleave="handleColDragLeave($event)"
                            @drop.prevent="handleColDrop($event, index)"
                            @dragend="draggedColIndex = null; dragOverColIndex = null"
                            :class="[col.width, dragOverColIndex === index ? 'bg-blue-200' : (col.type === 'custom' ? 'bg-indigo-50/70 text-indigo-950' : 'bg-slate-100')]"
                            class="p-2.5 border-r border-slate-200 cursor-move transition-colors relative group">
                            <div class="flex items-center justify-between gap-1">
                                <div class="flex items-center gap-1 truncate">
                                    <i class="fas fa-grip-vertical text-[10px] text-slate-400 opacity-40 group-hover:opacity-100"></i>
                                    <span x-text="col.name" :class="col.type === 'custom' ? 'text-indigo-900 font-black' : ''"></span>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    <!-- Sort button for standard sortable columns -->
                                    <template x-if="['tanggal_posting', 'jenis_konten', 'pillar', 'likes', 'comments', 'shares', 'saves', 'reposts', 'total_engagement', 'reach', 'new_followers'].includes(col.id)">
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

                    <th class="p-2.5 text-center w-16 bg-slate-100">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 font-medium text-slate-800">
                <!-- EXISTING ROWS WITH DRAG & DROP -->
                <template x-for="(post, idx) in displayedPosts" :key="post.id">
                    <tr draggable="true"
                        @dragstart="handleRowDragStart($event, idx)"
                        @dragover.prevent="handleRowDragOver($event, idx)"
                        @dragleave="handleRowDragLeave($event)"
                        @drop.prevent="handleRowDrop($event, idx)"
                        @dragend="draggedRowIndex = null; dragOverRowIndex = null"
                        :class="dragOverRowIndex === idx ? 'bg-blue-100' : 'hover:bg-blue-50/20'"
                        class="transition-colors group">
                        
                        <!-- No & Row Drag Handle -->
                        <td class="p-2 border-r border-slate-200 text-center text-slate-400 font-mono flex items-center justify-center gap-1 cursor-grab">
                            <i class="fas fa-grip-lines text-[10px] text-slate-300 group-hover:text-slate-500"></i>
                            <span x-text="idx + 1"></span>
                        </td>

                        <!-- DYNAMIC CELLS BASED ON TABLE COLUMNS -->
                        <template x-for="col in tableColumns" :key="col.id">
                            <td class="p-1 border-r border-slate-200" :class="col.type === 'custom' ? 'bg-indigo-50/10' : ''">
                                
                                <!-- Tanggal Posting -->
                                <template x-if="col.id === 'tanggal_posting'">
                                    <input type="date" :value="post.tanggal_posting ? post.tanggal_posting.split('T')[0] : ''" 
                                           @change="updateCell(post, 'tanggal_posting', $event.target.value)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-semibold text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                </template>

                                <!-- Jenis Konten -->
                                <template x-if="col.id === 'jenis_konten'">
                                    <select x-model="post.jenis_konten" 
                                            @change="updateCell(post, 'jenis_konten', post.jenis_konten)"
                                            class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs font-bold uppercase bg-transparent hover:bg-white focus:bg-white"
                                            :class="{
                                                'text-pink-600': post.jenis_konten === 'REELS',
                                                'text-blue-600': post.jenis_konten === 'FEED',
                                                'text-amber-600': post.jenis_konten === 'STORY',
                                                'text-slate-600': post.jenis_konten === 'OTHERS'
                                            }">
                                        <option value="FEED">FEED</option>
                                        <option value="REELS">REELS</option>
                                        <option value="STORY">STORY</option>
                                        <option value="OTHERS">OTHERS</option>
                                    </select>
                                </template>

                                <!-- Pillar -->
                                <template x-if="col.id === 'pillar'">
                                    <input type="text" x-model="post.pillar" 
                                           @blur="updateCell(post, 'pillar', post.pillar)"
                                           placeholder="-" 
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-700 bg-transparent hover:bg-white focus:bg-white">
                                </template>

                                <!-- Likes -->
                                <template x-if="col.id === 'likes'">
                                    <input type="number" min="0" x-model.number="post.likes" 
                                           @change="updateCell(post, 'likes', post.likes)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- Comments -->
                                <template x-if="col.id === 'comments'">
                                    <input type="number" min="0" x-model.number="post.comments" 
                                           @change="updateCell(post, 'comments', post.comments)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- Shares -->
                                <template x-if="col.id === 'shares'">
                                    <input type="number" min="0" x-model.number="post.shares" 
                                           @change="updateCell(post, 'shares', post.shares)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- Saves -->
                                <template x-if="col.id === 'saves'">
                                    <input type="number" min="0" x-model.number="post.saves" 
                                           @change="updateCell(post, 'saves', post.saves)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- Reposts -->
                                <template x-if="col.id === 'reposts'">
                                    <input type="number" min="0" x-model.number="post.reposts" 
                                           @change="updateCell(post, 'reposts', post.reposts)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- Total Engagement (Computed) -->
                                <template x-if="col.id === 'total_engagement'">
                                    <div class="p-1 text-center font-bold font-mono bg-slate-50/50 text-slate-900" x-text="formatNumber(post.total_engagement)"></div>
                                </template>

                                <!-- Reach -->
                                <template x-if="col.id === 'reach'">
                                    <input type="number" min="0" x-model.number="post.reach" 
                                           @change="updateCell(post, 'reach', post.reach)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- New Followers -->
                                <template x-if="col.id === 'new_followers'">
                                    <input type="number" min="0" x-model.number="post.new_followers" 
                                           @change="updateCell(post, 'new_followers', post.new_followers)"
                                           class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-center font-mono text-slate-800 bg-transparent hover:bg-white focus:bg-white font-semibold">
                                </template>

                                <!-- ER by Post -->
                                <template x-if="col.id === 'er_by_post'">
                                    <div class="p-1 text-center font-bold text-blue-600 font-mono" x-text="(post.er_by_post || 0) + '%'"></div>
                                </template>

                                <!-- ER by Reach -->
                                <template x-if="col.id === 'er_by_reach'">
                                    <div class="p-1 text-center font-bold text-purple-600 font-mono" x-text="(post.er_by_reach || 0) + '%'"></div>
                                </template>

                                <!-- Link Konten -->
                                <template x-if="col.id === 'link_konten'">
                                    <div class="flex items-center gap-1">
                                        <input type="url" x-model="post.link_konten" 
                                               @blur="updateCell(post, 'link_konten', post.link_konten)"
                                               placeholder="https://..." 
                                               class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-600 bg-transparent hover:bg-white focus:bg-white truncate">
                                        <template x-if="post.link_konten">
                                            <a :href="post.link_konten" target="_blank" class="text-slate-400 hover:text-pink-600 p-1 shrink-0" title="Buka Link">
                                                <i class="fas fa-external-link-alt text-[10px]"></i>
                                            </a>
                                        </template>
                                    </div>
                                </template>

                                <!-- CUSTOM COLUMN CELL (Dynamic Type) -->
                                <template x-if="col.type === 'custom'">
                                    <div>
                                        <!-- Text -->
                                        <template x-if="col.col_type === 'text' || !col.col_type">
                                            <input type="text" :value="(post.custom_fields && post.custom_fields[col.id]) ? post.custom_fields[col.id] : ''" 
                                                   @blur="saveCustomFieldCell(post, col.id, $event.target.value)" 
                                                   placeholder="Isi..." 
                                                   class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                        </template>
                                        <!-- Dropdown -->
                                        <template x-if="col.col_type === 'dropdown'">
                                            <select :value="(post.custom_fields && post.custom_fields[col.id]) ? post.custom_fields[col.id] : ''" 
                                                    @change="saveCustomFieldCell(post, col.id, $event.target.value)" 
                                                    class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs bg-transparent hover:bg-white focus:bg-white">
                                                <option value="">-- Pilih --</option>
                                                <template x-for="opt in parseOptions(col.options)" :key="opt">
                                                    <option :value="opt" x-text="opt" :selected="post.custom_fields && post.custom_fields[col.id] === opt"></option>
                                                </template>
                                            </select>
                                        </template>
                                        <!-- Checklist -->
                                        <template x-if="col.col_type === 'checklist'">
                                            <div class="flex items-center justify-center py-1">
                                                <input type="checkbox" :checked="post.custom_fields && post.custom_fields[col.id] === 'ya'" 
                                                       @change="saveCustomFieldCell(post, col.id, $event.target.checked ? 'ya' : 'tidak')" 
                                                       class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                            </div>
                                        </template>
                                        <!-- Date -->
                                        <template x-if="col.col_type === 'date'">
                                            <input type="date" :value="(post.custom_fields && post.custom_fields[col.id]) ? post.custom_fields[col.id] : ''" 
                                                   @change="saveCustomFieldCell(post, col.id, $event.target.value)" 
                                                   class="w-full px-2 py-1 border-none focus:ring-2 focus:ring-blue-500 rounded-lg text-xs text-slate-800 bg-transparent hover:bg-white focus:bg-white">
                                        </template>
                                    </div>
                                </template>

                            </td>
                        </template>

                        <!-- Actions -->
                        <td class="p-2 text-center">
                            <button type="button" @click="deleteRow(post, idx)" class="text-slate-300 hover:text-red-600 p-1 transition-colors" title="Hapus Baris">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </td>
                    </tr>
                </template>

                <!-- INLINE QUICK ADD ROW (BOTTOM OF TABLE) -->
                <tr class="bg-emerald-50/20 border-t-2 border-emerald-400">
                    <td class="p-2 border-r border-slate-200 text-center font-bold text-emerald-600">
                        <i class="fas fa-plus"></i>
                    </td>
                    
                    <template x-for="col in tableColumns" :key="'new_' + col.id">
                        <td class="p-1 border-r border-slate-200">
                            <!-- Tanggal -->
                            <template x-if="col.id === 'tanggal_posting'">
                                <input type="date" x-model="newRow.tanggal_posting" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs font-semibold bg-white">
                            </template>

                            <!-- Jenis -->
                            <template x-if="col.id === 'jenis_konten'">
                                <select x-model="newRow.jenis_konten" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs font-bold uppercase bg-white">
                                    <option value="FEED">FEED</option>
                                    <option value="REELS">REELS</option>
                                    <option value="STORY">STORY</option>
                                    <option value="OTHERS">OTHERS</option>
                                </select>
                            </template>

                            <!-- Pillar -->
                            <template x-if="col.id === 'pillar'">
                                <input type="text" x-model="newRow.pillar" placeholder="Pillar..." class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs bg-white">
                            </template>

                            <!-- Likes -->
                            <template x-if="col.id === 'likes'">
                                <input type="number" min="0" x-model.number="newRow.likes" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- Comments -->
                            <template x-if="col.id === 'comments'">
                                <input type="number" min="0" x-model.number="newRow.comments" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- Shares -->
                            <template x-if="col.id === 'shares'">
                                <input type="number" min="0" x-model.number="newRow.shares" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- Saves -->
                            <template x-if="col.id === 'saves'">
                                <input type="number" min="0" x-model.number="newRow.saves" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- Reposts -->
                            <template x-if="col.id === 'reposts'">
                                <input type="number" min="0" x-model.number="newRow.reposts" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- Total Engagement -->
                            <template x-if="col.id === 'total_engagement'">
                                <div class="p-1 text-center font-bold font-mono text-slate-400">-</div>
                            </template>

                            <!-- Reach -->
                            <template x-if="col.id === 'reach'">
                                <input type="number" min="0" x-model.number="newRow.reach" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- New Followers -->
                            <template x-if="col.id === 'new_followers'">
                                <input type="number" min="0" x-model.number="newRow.new_followers" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs text-center font-mono bg-white font-semibold">
                            </template>

                            <!-- ERs -->
                            <template x-if="col.id === 'er_by_post' || col.id === 'er_by_reach'">
                                <div class="p-1 text-center text-slate-400 font-mono">-</div>
                            </template>

                            <!-- Link Konten -->
                            <template x-if="col.id === 'link_konten'">
                                <input type="url" x-model="newRow.link_konten" placeholder="https://..." class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs bg-white">
                            </template>

                            <!-- Custom Column Input for New Row -->
                            <template x-if="col.type === 'custom'">
                                <div>
                                    <template x-if="col.col_type === 'text' || !col.col_type">
                                        <input type="text" x-model="newRow.custom_fields[col.id]" placeholder="Isi..." class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs bg-white">
                                    </template>
                                    <template x-if="col.col_type === 'dropdown'">
                                        <select x-model="newRow.custom_fields[col.id]" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs bg-white">
                                            <option value="">-- Pilih --</option>
                                            <template x-for="opt in parseOptions(col.options)" :key="opt">
                                                <option :value="opt" x-text="opt"></option>
                                            </template>
                                        </select>
                                    </template>
                                    <template x-if="col.col_type === 'checklist'">
                                        <div class="flex items-center justify-center py-1">
                                            <input type="checkbox" @change="newRow.custom_fields[col.id] = $event.target.checked ? 'ya' : 'tidak'" class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        </div>
                                    </template>
                                    <template x-if="col.col_type === 'date'">
                                        <input type="date" x-model="newRow.custom_fields[col.id]" class="w-full px-2 py-1 border border-emerald-300 rounded-lg text-xs bg-white">
                                    </template>
                                </div>
                            </template>

                        </td>
                    </template>

                    <!-- Save New Row Button -->
                    <td class="p-1 text-center">
                        <button type="button" @click="submitQuickAdd()" :disabled="isSubmitting" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-lg font-bold text-xs shadow-xs transition-colors flex items-center justify-center gap-1 mx-auto" title="Simpan Baris Ini">
                            <i class="fas fa-check"></i> <span class="hidden sm:inline">Tambah</span>
                        </button>
                    </td>
                </tr>

                <template x-if="displayedPosts.length === 0">
                    <tr>
                        <td :colspan="tableColumns.length + 2" class="p-8 text-center text-slate-400">
                            <i class="fas fa-inbox text-2xl mb-1"></i>
                            <p class="text-xs">Tidak ada data postingan Instagram pada filter ini.</p>
                        </td>
                    </tr>
                </template>
            </tbody>

            <!-- FOOTER SUMMARY BAR (TOTALS) -->
            <tfoot class="sticky bottom-0 bg-slate-900 text-white font-bold text-[11px] select-none z-20">
                <tr>
                    <td class="p-2.5 text-center text-slate-400 border-r border-slate-700">∑</td>
                    
                    <template x-for="col in tableColumns" :key="'foot_' + col.id">
                        <td class="p-2.5 border-r border-slate-700 text-center font-mono">
                            <template x-if="col.id === 'tanggal_posting'">
                                <div class="text-left font-extrabold tracking-wider uppercase text-slate-300">
                                    TOTAL (<span x-text="displayedPosts.length"></span>)
                                </div>
                            </template>
                            <template x-if="col.id === 'likes'">
                                <span x-text="formatNumber(summary.likes)"></span>
                            </template>
                            <template x-if="col.id === 'comments'">
                                <span x-text="formatNumber(summary.comments)"></span>
                            </template>
                            <template x-if="col.id === 'shares'">
                                <span x-text="formatNumber(summary.shares)"></span>
                            </template>
                            <template x-if="col.id === 'saves'">
                                <span x-text="formatNumber(summary.saves)"></span>
                            </template>
                            <template x-if="col.id === 'reposts'">
                                <span x-text="formatNumber(summary.reposts)"></span>
                            </template>
                            <template x-if="col.id === 'total_engagement'">
                                <span class="text-pink-300 font-black" x-text="formatNumber(summary.total_engagement)"></span>
                            </template>
                            <template x-if="col.id === 'reach'">
                                <span x-text="formatNumber(summary.reach)"></span>
                            </template>
                            <template x-if="col.id === 'new_followers'">
                                <span class="text-emerald-300" x-text="formatNumber(summary.new_followers)"></span>
                            </template>
                            <template x-if="col.id === 'er_by_post'">
                                <span class="text-blue-300" x-text="summary.avg_er_post + '%'"></span>
                            </template>
                            <template x-if="col.id === 'er_by_reach'">
                                <span class="text-purple-300" x-text="summary.avg_er_reach + '%'"></span>
                            </template>
                            <template x-if="!['tanggal_posting', 'likes', 'comments', 'shares', 'saves', 'reposts', 'total_engagement', 'reach', 'new_followers', 'er_by_post', 'er_by_reach'].includes(col.id)">
                                <span class="text-slate-600">-</span>
                            </template>
                        </td>
                    </template>

                    <td class="p-2.5 text-center text-slate-500"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- MODAL TAMBAH KOLOM KUSTOM -->
    <div x-show="showAddColumnModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
        <div @click.away="showAddColumnModal = false" class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full p-5 overflow-hidden transform transition-all">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="font-bold text-slate-800 text-sm flex items-center gap-2">
                    <i class="fas fa-columns text-pink-600"></i> Tambah Kolom Kustom Spreadsheet
                </h3>
                <button type="button" @click="showAddColumnModal = false" class="text-slate-400 hover:text-slate-600 p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form @submit.prevent="submitNewColumn()" class="mt-4 space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kolom <span class="text-red-500">*</span></label>
                    <input type="text" x-model="newColumnName" required placeholder="Contoh: Designer, Status Revisi, Target Market..." class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Tipe Isi Kolom <span class="text-red-500">*</span></label>
                    <select x-model="newColumnType" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500 font-medium">
                        <option value="text">Teks Bebas (Bisa diedit langsung)</option>
                        <option value="dropdown">Pilihan Dropdown (Bisa pilih opsi)</option>
                        <option value="checklist">Checklist (Ya / Tidak)</option>
                        <option value="date">Tanggal</option>
                    </select>
                </div>

                <div x-show="newColumnType === 'dropdown'" x-collapse>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Opsi Pilihan (Pisahkan dengan koma) <span class="text-red-500">*</span></label>
                    <input type="text" x-model="newColumnOptions" placeholder="Contoh: Desain Siap, Revisi, ACC, Uploaded" class="w-full px-3 py-2 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500">
                    <p class="text-[10px] text-slate-400 mt-1">Ketik opsi-opsi yang dapat dipilih dipisahkan tanda koma.</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" @click="showAddColumnModal = false" class="px-3.5 py-1.5 border border-slate-200 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" :disabled="isSubmittingColumn" class="px-4 py-1.5 bg-pink-600 hover:bg-pink-700 disabled:opacity-50 text-white rounded-xl text-xs font-bold shadow-xs flex items-center gap-1.5">
                        <i class="fas fa-plus"></i> <span x-text="isSubmittingColumn ? 'Menyimpan...' : 'Tambahkan Kolom'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function instagramSpreadsheet() {
    return {
        posts: @json($posts),
        customColumns: @json($customColumns),
        tableColumns: @json($tableColumns),
        showQuickAdd: false,
        isSubmitting: false,
        sortColumn: 'tanggal_posting',
        sortDirection: 'desc',
        filterType: '',
        searchQuery: '',
        draggedColIndex: null,
        dragOverColIndex: null,
        draggedRowIndex: null,
        dragOverRowIndex: null,

        showAddColumnModal: false,
        newColumnName: '',
        newColumnType: 'text',
        newColumnOptions: '',
        isSubmittingColumn: false,

        newRow: {
            tanggal_posting: '{{ date("Y-m-d") }}',
            jenis_konten: 'FEED',
            pillar: '',
            likes: 0,
            comments: 0,
            shares: 0,
            saves: 0,
            reposts: 0,
            reach: 0,
            new_followers: 0,
            link_konten: '',
            custom_fields: {}
        },

        get displayedPosts() {
            let result = [...this.posts];

            // Filter Jenis
            if (this.filterType) {
                result = result.filter(p => p.jenis_konten === this.filterType);
            }

            // Search query
            if (this.searchQuery.trim() !== '') {
                const q = this.searchQuery.toLowerCase();
                result = result.filter(p => {
                    const date = (p.tanggal_posting || '').toLowerCase();
                    const pillar = (p.pillar || '').toLowerCase();
                    return date.includes(q) || pillar.includes(q);
                });
            }

            // Sorting
            if (this.sortColumn) {
                const col = this.sortColumn;
                const dir = this.sortDirection === 'asc' ? 1 : -1;
                result.sort((a, b) => {
                    let valA = a[col];
                    let valB = b[col];
                    if (valA === null || valA === undefined) valA = '';
                    if (valB === null || valB === undefined) valB = '';

                    if (typeof valA === 'number' && typeof valB === 'number') {
                        return (valA - valB) * dir;
                    }
                    return String(valA).localeCompare(String(valB)) * dir;
                });
            }

            return result;
        },

        get summary() {
            const list = this.displayedPosts;
            const totals = {
                likes: 0,
                comments: 0,
                shares: 0,
                saves: 0,
                reposts: 0,
                total_engagement: 0,
                reach: 0,
                new_followers: 0,
                er_post_sum: 0,
                er_reach_sum: 0,
                avg_er_post: '0',
                avg_er_reach: '0'
            };

            if (list.length === 0) return totals;

            list.forEach(p => {
                totals.likes += Number(p.likes || 0);
                totals.comments += Number(p.comments || 0);
                totals.shares += Number(p.shares || 0);
                totals.saves += Number(p.saves || 0);
                totals.reposts += Number(p.reposts || 0);
                totals.total_engagement += Number(p.total_engagement || 0);
                totals.reach += Number(p.reach || 0);
                totals.new_followers += Number(p.new_followers || 0);
                totals.er_post_sum += Number(p.er_by_post || 0);
                totals.er_reach_sum += Number(p.er_by_reach || 0);
            });

            totals.avg_er_post = (totals.er_post_sum / list.length).toFixed(2);
            totals.avg_er_reach = (totals.er_reach_sum / list.length).toFixed(2);

            return totals;
        },

        toggleSort(col) {
            if (this.sortColumn === col) {
                if (this.sortDirection === 'asc') {
                    this.sortDirection = 'desc';
                } else {
                    this.sortColumn = '';
                    this.sortDirection = 'asc';
                }
            } else {
                this.sortColumn = col;
                this.sortDirection = 'asc';
            }
        },

        formatNumber(num) {
            if (num === null || num === undefined) return '0';
            return Number(num).toLocaleString('id-ID');
        },

        parseOptions(optStr) {
            if (!optStr) return [];
            return optStr.split(',').map(s => s.trim()).filter(s => s.length > 0);
        },

        // Drag & Drop Columns
        handleColDragStart(e, index) {
            this.draggedColIndex = index;
            e.dataTransfer.effectAllowed = 'move';
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
            
            const item = this.tableColumns.splice(this.draggedColIndex, 1)[0];
            this.tableColumns.splice(dropIndex, 0, item);
            
            this.draggedColIndex = null;
            this.dragOverColIndex = null;

            // Persist order to server
            let newOrder = this.tableColumns.map(c => c.id);
            try {
                await fetch("{{ route('admin.instagram-posts.columns.order') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ columns: newOrder })
                });
            } catch(err) {
                console.error("Failed to save column order", err);
            }
        },

        // Drag & Drop Rows
        handleRowDragStart(e, index) {
            this.draggedRowIndex = index;
            e.dataTransfer.effectAllowed = 'move';
        },
        handleRowDragOver(e, index) {
            if (this.draggedRowIndex !== null && this.draggedRowIndex !== index) {
                this.dragOverRowIndex = index;
            }
        },
        handleRowDragLeave(e) {
            this.dragOverRowIndex = null;
        },
        handleRowDrop(e, dropIndex) {
            if (this.draggedRowIndex === null || this.draggedRowIndex === dropIndex) return;
            
            const item = this.posts.splice(this.draggedRowIndex, 1)[0];
            this.posts.splice(dropIndex, 0, item);
            
            this.draggedRowIndex = null;
            this.dragOverRowIndex = null;
        },

        // Update Cell Inline
        async updateCell(post, field, value) {
            post[field] = value;
            post.total_engagement = Number(post.likes || 0) + Number(post.comments || 0) + Number(post.shares || 0) + Number(post.saves || 0) + Number(post.reposts || 0);
            if (Number(post.reach || 0) > 0) {
                post.er_by_reach = ((post.total_engagement / Number(post.reach)) * 100).toFixed(2);
            }

            try {
                const res = await fetch(`/admin/instagram-posts/${post.id}/inline-update`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ field, value })
                });
                const data = await res.json();
                if (data.success) {
                    post.total_engagement = data.total_engagement;
                    post.er_by_post = data.er_by_post;
                    post.er_by_reach = data.er_by_reach;
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal memperbarui sel.' });
                }
            } catch (err) {
                console.error(err);
            }
        },

        // Save Custom Field Cell
        async saveCustomFieldCell(post, columnId, value) {
            if (!post.custom_fields) post.custom_fields = {};
            post.custom_fields[columnId] = value;

            try {
                const res = await fetch(`/admin/instagram-posts/${post.id}/inline-update`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        field: 'custom_field',
                        column_id: columnId,
                        value: value
                    })
                });
                const data = await res.json();
                if (!data.success) {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menyimpan perubahan kolom.' });
                }
            } catch(err) {
                console.error("Error saving custom field", err);
            }
        },

        // Submit Quick Add (Row)
        async submitQuickAdd() {
            if (!this.newRow.tanggal_posting) {
                Swal.fire({ icon: 'warning', title: 'Peringatan', text: 'Tanggal posting wajib diisi.' });
                return;
            }

            this.isSubmitting = true;
            try {
                const res = await fetch("{{ route('admin.instagram-posts.quick-store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.newRow)
                });
                const data = await res.json();
                if (data.success) {
                    this.posts.unshift(data.post);
                    this.newRow = {
                        tanggal_posting: '{{ date("Y-m-d") }}',
                        jenis_konten: 'FEED',
                        pillar: '',
                        likes: 0,
                        comments: 0,
                        shares: 0,
                        saves: 0,
                        reposts: 0,
                        reach: 0,
                        new_followers: 0,
                        link_konten: '',
                        custom_fields: {}
                    };
                    this.showQuickAdd = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Postingan Instagram baru berhasil ditambahkan ke spreadsheet!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menambahkan postingan.' });
                }
            } catch (err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            } finally {
                this.isSubmitting = false;
            }
        },

        // Delete Row
        deleteRow(post, idx) {
            Swal.fire({
                title: 'Hapus Postingan Ini?',
                text: `Data tanggal ${post.tanggal_posting ? post.tanggal_posting.split('T')[0] : ''} (${post.jenis_konten}) akan dihapus permanen!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = `/admin/instagram-posts/${post.id}`;
                        form.innerHTML = `
                            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
                            <input type="hidden" name="_method" value="DELETE">
                        `;
                        document.body.appendChild(form);
                        form.submit();
                    } catch (err) {
                        console.error(err);
                    }
                }
            });
        },

        // Custom Column Modal & Submit
        openAddColumnModal() {
            this.newColumnName = '';
            this.newColumnType = 'text';
            this.newColumnOptions = '';
            this.showAddColumnModal = true;
        },

        async submitNewColumn() {
            if (!this.newColumnName.trim()) return;
            if (this.newColumnType === 'dropdown' && !this.newColumnOptions.trim()) {
                Swal.fire({ icon: 'warning', title: 'Perhatian', text: 'Opsi dropdown wajib diisi (pisahkan dengan koma).' });
                return;
            }

            this.isSubmittingColumn = true;
            try {
                const res = await fetch("{{ route('admin.instagram-posts.columns.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        name: this.newColumnName.trim(),
                        type: this.newColumnType,
                        options: this.newColumnOptions.trim()
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.customColumns = data.columns;
                    this.tableColumns.push({
                        id: data.new_column.id,
                        name: data.new_column.name,
                        type: 'custom',
                        col_type: data.new_column.type,
                        options: data.new_column.options,
                        width: 'min-w-[160px]'
                    });

                    // Update order on server
                    let newOrder = this.tableColumns.map(c => c.id);
                    await fetch("{{ route('admin.instagram-posts.columns.order') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ columns: newOrder })
                    });

                    this.showAddColumnModal = false;
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menambahkan kolom.' });
                }
            } catch(err) {
                console.error(err);
                Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
            } finally {
                this.isSubmittingColumn = false;
            }
        },

        async deleteColumn(col, index) {
            const result = await Swal.fire({
                title: 'Hapus Kolom Kustom?',
                text: `Kolom "${col.name}" dan seluruh datanya akan dihapus dari spreadsheet!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus Kolom!',
                cancelButtonText: 'Batal'
            });

            if (result.isConfirmed) {
                try {
                    const res = await fetch(`/admin/instagram-posts/columns/${col.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.customColumns = data.columns;
                        this.tableColumns = this.tableColumns.filter(c => c.id !== col.id);
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus',
                            text: data.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal menghapus kolom.' });
                    }
                } catch(err) {
                    console.error(err);
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan koneksi.' });
                }
            }
        }
    };
}
</script>
@endpush
@endsection
n

