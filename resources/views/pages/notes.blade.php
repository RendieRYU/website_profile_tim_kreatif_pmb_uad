@extends('layouts.app')

@section('content')
<x-public-blur-lock>
<div class="py-8 bg-slate-50 min-h-screen" x-data="publicNotesApp()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
            <div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-amber-500"></i> Catatan & Ide Tim
                </h1>
                <p class="text-slate-500 text-sm mt-1">Kumpulan modul catatan, referensi, dan ide kreatif publik</p>
            </div>

            <!-- Search Bar -->
            <div class="relative w-full md:w-72">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari catatan..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-amber-500">
            </div>
        </div>

        <!-- SECTION 1: RECENT FOLDERS -->
        <div>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
                <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-folder-open text-blue-500"></i> Folder Utama
                </h2>

                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 shadow-2xs">
                    <button @click="selectedFolderId = null" :class="selectedFolderId === null ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Semua Folder</button>
                    <template x-for="f in folders" :key="f.id">
                        <button @click="selectedFolderId = f.id" :class="selectedFolderId === f.id ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all truncate max-w-[120px]" x-text="f.name"></button>
                    </template>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="folder in folders" :key="folder.id">
                    <div @click="selectedFolderId = (selectedFolderId === folder.id ? null : folder.id)"
                         :style="'background-color: ' + folder.color"
                         :class="{'ring-2 ring-slate-900 shadow-md scale-[1.02]': selectedFolderId === folder.id}"
                         class="p-5 rounded-3xl border border-black/5 shadow-2xs hover:shadow-md transition-all cursor-pointer flex flex-col justify-between h-36">
                        
                        <div class="w-10 h-10 rounded-xl bg-black/10 flex items-center justify-center text-slate-800">
                            <i class="fas fa-folder text-lg"></i>
                        </div>

                        <div>
                            <h3 class="font-extrabold text-slate-900 text-base leading-snug truncate" x-text="folder.name"></h3>
                            <p class="text-xs text-slate-600 font-medium mt-0.5" x-text="(folder.notes_count || getFolderNoteCount(folder.id)) + ' Catatan'"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- SECTION 2: STICKY NOTES GRID -->
        <div>
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
                <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-amber-500"></i> Catatan Saya
                    <template x-if="selectedFolderId">
                        <span class="text-xs font-semibold px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full flex items-center gap-1">
                            Folder: <span x-text="getFolderName(selectedFolderId)"></span>
                            <button @click="selectedFolderId = null" class="hover:text-blue-900 ml-1"><i class="fas fa-times"></i></button>
                        </span>
                    </template>
                </h2>

                <div class="flex items-center gap-1 bg-white p-1 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 shadow-2xs">
                    <button @click="timeFilter = 'all'" :class="timeFilter === 'all' ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Semua</button>
                    <button @click="timeFilter = 'today'" :class="timeFilter === 'today' ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Hari Ini</button>
                    <button @click="timeFilter = 'week'" :class="timeFilter === 'week' ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Minggu Ini</button>
                    <button @click="timeFilter = 'month'" :class="timeFilter === 'month' ? 'bg-slate-900 text-white font-bold' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Bulan Ini</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                <template x-for="note in filteredNotes" :key="note.id">
                    <div @click="selectedNote = note"
                         :style="'background-color: ' + note.color"
                         class="p-5 rounded-3xl border border-black/5 shadow-2xs hover:shadow-lg transition-all flex flex-col justify-between h-64 relative group cursor-pointer">
                        
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-[11px] font-semibold text-slate-600 font-mono tracking-tight" x-text="formatDate(note.note_date || note.created_at)"></span>
                                <template x-if="note.is_pinned">
                                    <span class="text-amber-700 font-bold" title="Dipin"><i class="fas fa-thumbtack text-xs"></i></span>
                                </template>
                            </div>

                            <template x-if="note.folder">
                                <span class="inline-block px-2 py-0.5 bg-black/10 text-slate-800 rounded-md text-[10px] font-bold mb-2 truncate max-w-full" x-text="note.folder.name"></span>
                            </template>

                            <h3 class="font-extrabold text-slate-900 text-lg leading-tight mb-2 line-clamp-1" x-text="note.title"></h3>

                            <p class="text-xs text-slate-700 leading-relaxed line-clamp-3 whitespace-pre-wrap" x-text="note.content || 'Tidak ada pratinjau teks...'"></p>
                        </div>

                        <div class="pt-3 border-t border-black/10 flex justify-between items-center text-xs text-slate-600">
                            <div class="flex items-center gap-1.5">
                                <i class="far fa-clock text-[11px]"></i>
                                <span class="text-[10px] font-medium" x-text="formatTime(note.updated_at)"></span>
                            </div>
                            <span class="text-[10px] font-bold text-slate-800">Baca <i class="fas fa-arrow-right text-[8px]"></i></span>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="filteredNotes.length === 0">
                <div class="text-center py-12 bg-white rounded-3xl border border-slate-100 shadow-sm mt-4">
                    <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-sticky-note text-2xl"></i>
                    </div>
                    <h4 class="font-bold text-slate-800 text-base">Belum Ada Catatan Publik</h4>
                    <p class="text-slate-400 text-xs mt-1">Catatan publik akan ditampilkan di sini.</p>
                </div>
            </template>
        </div>

        <!-- PUBLIC READ NOTE MODAL -->
        <div x-show="selectedNote" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="selectedNote = null"></div>
            <div class="bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 relative z-10" :style="'background-color: ' + (selectedNote?.color || '#ffffff')">
                <button @click="selectedNote = null" class="absolute top-4 right-4 text-slate-600 hover:text-slate-900"><i class="fas fa-times text-lg"></i></button>

                <div class="flex items-center gap-2 mb-2" x-show="selectedNote?.folder">
                    <span class="px-2.5 py-0.5 bg-black/10 text-slate-900 font-bold text-xs rounded-full uppercase" x-text="selectedNote?.folder?.name"></span>
                </div>

                <h3 class="text-2xl font-extrabold text-slate-900 mb-2 leading-snug" x-text="selectedNote?.title"></h3>
                <p class="text-xs font-mono text-slate-600 mb-4" x-text="formatDate(selectedNote?.note_date || selectedNote?.created_at) + ' • ' + formatTime(selectedNote?.updated_at)"></p>

                <div class="p-4 bg-white/70 backdrop-blur-xs rounded-2xl border border-black/5 text-xs text-slate-800 leading-relaxed whitespace-pre-wrap max-h-96 overflow-y-auto mb-6" x-text="selectedNote?.content || 'Tidak ada isi catatan.'"></div>

                <div class="flex items-center justify-end">
                    <button type="button" @click="selectedNote = null" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm">Tutup</button>
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
        Alpine.data('publicNotesApp', () => ({
            folders: {!! json_encode($folders) !!},
            notes: {!! json_encode($notes) !!},
            selectedFolderId: null,
            timeFilter: 'all',
            searchQuery: '',
            selectedNote: null,

            get filteredNotes() {
                return this.notes.filter(note => {
                    if (this.selectedFolderId !== null && note.folder_id != this.selectedFolderId) return false;
                    if (this.searchQuery.trim() !== '') {
                        let q = this.searchQuery.toLowerCase();
                        let matchTitle = note.title && note.title.toLowerCase().includes(q);
                        let matchContent = note.content && note.content.toLowerCase().includes(q);
                        if (!matchTitle && !matchContent) return false;
                    }
                    if (this.timeFilter !== 'all') {
                        let noteDate = new Date(note.note_date || note.created_at);
                        let today = new Date();
                        if (this.timeFilter === 'today') {
                            return noteDate.toDateString() === today.toDateString();
                        } else if (this.timeFilter === 'week') {
                            let diffTime = Math.abs(today - noteDate);
                            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                            return diffDays <= 7;
                        } else if (this.timeFilter === 'month') {
                            return noteDate.getMonth() === today.getMonth() && noteDate.getFullYear() === today.getFullYear();
                        }
                    }
                    return true;
                });
            },

            getFolderNoteCount(folderId) {
                return this.notes.filter(n => n.folder_id == folderId).length;
            },

            getFolderName(folderId) {
                let f = this.folders.find(f => f.id == folderId);
                return f ? f.name : '';
            },

            formatDate(dateStr) {
                if (!dateStr) return '';
                let d = new Date(dateStr);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
            },

            formatTime(dateStr) {
                if (!dateStr) return '';
                let d = new Date(dateStr);
                return d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
            }
        }));
    });
</script>
@endpush
