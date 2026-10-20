@extends('layouts.admin')

@section('content')
<div x-data="notesApp()" class="space-y-8">
    <!-- Header Title & Live Search Bar -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-slate-900 uppercase">CATATAN SAYA</h1>
            <p class="text-slate-500 text-sm mt-1">Kelola folder, ide, dan catatan penting dengan antarmuka interaktif</p>
        </div>

        <div class="flex items-center gap-3 w-full md:w-auto">
            <!-- Search Bar -->
            <div class="relative flex-1 md:w-72">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                <input type="text" x-model="searchQuery" placeholder="Cari catatan..." class="w-full pl-10 pr-4 py-2.5 bg-white border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-xs">
            </div>

            <!-- Quick Add Note Button -->
            <button @click="openNoteModal()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-semibold flex items-center gap-2 shadow-sm transition-all shrink-0">
                <i class="fas fa-plus"></i>
                <span>Catatan Baru</span>
            </button>
        </div>
    </div>

    <!-- SECTION 1: RECENT FOLDERS -->
    <div>
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 mb-4">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-folder-open text-blue-500"></i> Folder Utama
            </h2>

            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg text-xs font-semibold text-slate-600">
                <button @click="selectedFolderId = null" :class="selectedFolderId === null ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1 rounded-md transition-all">Semua Folder</button>
                <template x-for="f in folders" :key="f.id">
                    <button @click="selectedFolderId = f.id" :class="selectedFolderId === f.id ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1 rounded-md transition-all truncate max-w-[120px]" x-text="f.name"></button>
                </template>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <template x-for="folder in folders" :key="folder.id">
                <div @dragover.prevent="dragOverFolderId = folder.id"
                     @dragleave="dragOverFolderId = null"
                     @drop.prevent="dropNoteToFolder(folder.id)"
                     @click="selectedFolderId = (selectedFolderId === folder.id ? null : folder.id)"
                     :style="'background-color: ' + folder.color"
                     :class="{'ring-2 ring-blue-500 shadow-md scale-[1.02]': selectedFolderId === folder.id, 'border-2 border-dashed border-blue-600 scale-[1.03]': dragOverFolderId === folder.id}"
                     class="p-5 rounded-2xl border border-black/5 shadow-xs hover:shadow-md transition-all cursor-pointer relative group flex flex-col justify-between h-36">
                    
                    <div class="flex justify-between items-start">
                        <div class="w-10 h-10 rounded-xl bg-black/10 flex items-center justify-center text-slate-800">
                            <i class="fas fa-folder text-lg"></i>
                        </div>

                        <div class="relative" @click.stop>
                            <button @click="folderMenuId = (folderMenuId === folder.id ? null : folder.id)" class="w-8 h-8 rounded-lg hover:bg-black/10 flex items-center justify-center text-slate-600 transition-colors">
                                <i class="fas fa-ellipsis-h text-sm"></i>
                            </button>
                            <!-- Dropdown Menu -->
                            <div x-show="folderMenuId === folder.id" @click.outside="folderMenuId = null" class="absolute right-0 top-9 bg-white text-slate-800 rounded-xl shadow-xl border border-slate-100 py-1.5 w-36 z-20" x-cloak>
                                <button @click="deleteFolder(folder)" class="w-full px-3 py-1.5 text-left text-xs text-red-600 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-trash"></i> Hapus Folder
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-slate-900 text-base leading-snug truncate" x-text="folder.name"></h3>
                        <p class="text-xs text-slate-600 font-medium mt-0.5" x-text="(folder.notes_count || getFolderNoteCount(folder.id)) + ' Catatan'"></p>
                    </div>
                </div>
            </template>

            <!-- Dotted New Folder Button -->
            <button @click="openFolderModal()" class="h-36 rounded-2xl border-2 border-dashed border-slate-300 hover:border-blue-500 hover:bg-blue-50/30 transition-all flex flex-col items-center justify-center gap-2 text-slate-500 hover:text-blue-600 font-semibold text-sm group">
                <div class="w-10 h-10 rounded-xl border border-slate-300 group-hover:border-blue-500 group-hover:bg-blue-50 flex items-center justify-center transition-colors">
                    <i class="fas fa-folder-plus text-base"></i>
                </div>
                <span>+ Folder Baru</span>
            </button>
        </div>
    </div>

    <!-- SECTION 2: MY NOTES (STICKY NOTES GRID) -->
    <div>
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-4">
            <h2 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-sticky-note text-amber-500"></i> Catatan Saya
                <template x-if="selectedFolderId">
                    <span class="text-xs font-semibold px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full flex items-center gap-1">
                        Folder: <span x-text="getFolderName(selectedFolderId)"></span>
                        <button @click="selectedFolderId = null" class="hover:text-blue-900 ml-1"><i class="fas fa-times"></i></button>
                    </span>
                </template>
            </h2>

            <!-- Time Filter Tabs -->
            <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs font-semibold text-slate-600">
                <button @click="timeFilter = 'all'" :class="timeFilter === 'all' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Semua</button>
                <button @click="timeFilter = 'today'" :class="timeFilter === 'today' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Hari Ini</button>
                <button @click="timeFilter = 'week'" :class="timeFilter === 'week' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Minggu Ini</button>
                <button @click="timeFilter = 'month'" :class="timeFilter === 'month' ? 'bg-white text-slate-900 font-bold shadow-xs' : 'hover:text-slate-900'" class="px-3 py-1.5 rounded-lg transition-all">Bulan Ini</button>
            </div>
        </div>

        <!-- Sticky Notes Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            <template x-for="note in filteredNotes" :key="note.id">
                <div draggable="true"
                     @dragstart="handleNoteDragStart($event, note)"
                     @dragover.prevent="handleNoteDragOver($event, note)"
                     @drop.prevent="handleNoteDrop($event, note)"
                     :style="'background-color: ' + note.color"
                     :class="{'ring-2 ring-amber-400 shadow-md': note.is_pinned, 'opacity-50 scale-95': draggedNoteId === note.id}"
                     class="p-5 rounded-3xl border border-black/5 shadow-xs hover:shadow-lg transition-all flex flex-col justify-between h-64 relative group cursor-grab active:cursor-grabbing">
                    
                    <div>
                        <!-- Note Header: Date & Controls -->
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[11px] font-semibold text-slate-600 font-mono tracking-tight" x-text="formatDate(note.note_date || note.created_at)"></span>
                            
                            <div class="flex items-center gap-1">
                                <button @click.stop="togglePin(note)" 
                                        :class="note.is_pinned ? 'text-amber-700 font-bold scale-110' : 'text-slate-400 hover:text-slate-700'"
                                        class="w-7 h-7 rounded-lg hover:bg-black/10 flex items-center justify-center transition-colors" title="Sematkan Catatan">
                                    <i class="fas fa-thumbtack text-xs"></i>
                                </button>
                                <button @click.stop="openNoteModal(note)" class="w-7 h-7 rounded-lg hover:bg-black/10 flex items-center justify-center text-slate-600 transition-colors" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Folder Badge if assigned -->
                        <template x-if="note.folder">
                            <span class="inline-block px-2 py-0.5 bg-black/10 text-slate-800 rounded-md text-[10px] font-bold mb-2 truncate max-w-full" x-text="note.folder.name"></span>
                        </template>

                        <!-- Note Title -->
                        <h3 class="font-extrabold text-slate-900 text-lg leading-tight mb-2 line-clamp-1" x-text="note.title"></h3>

                        <!-- Note Preview Content -->
                        <p class="text-xs text-slate-700 leading-relaxed line-clamp-3 whitespace-pre-wrap" x-text="note.content || 'Tidak ada pratinjau teks...'"></p>
                    </div>

                    <!-- Note Footer Controls -->
                    <div class="pt-3 border-t border-black/10 flex justify-between items-center text-xs text-slate-600">
                        <div class="flex items-center gap-1.5">
                            <i class="far fa-clock text-[11px]"></i>
                            <span class="text-[10px] font-medium" x-text="formatTime(note.updated_at)"></span>
                        </div>

                        <div class="flex items-center gap-1">
                            <!-- Color Picker Quick Select -->
                            <div class="relative" @click.stop>
                                <button @click="colorPickerNoteId = (colorPickerNoteId === note.id ? null : note.id)" class="w-6 h-6 rounded-full border border-black/20 flex items-center justify-center hover:scale-110 transition-transform" :style="'background-color: ' + note.color">
                                </button>
                                <div x-show="colorPickerNoteId === note.id" @click.outside="colorPickerNoteId = null" class="absolute right-0 bottom-8 bg-white p-2 rounded-xl shadow-xl border border-slate-100 flex gap-1.5 z-20" x-cloak>
                                    <template x-for="c in colorPalette">
                                        <button @click="changeNoteColor(note, c)" class="w-5 h-5 rounded-full border border-black/10 hover:scale-125 transition-transform" :style="'background-color: ' + c"></button>
                                    </template>
                                </div>
                            </div>

                            <button @click.stop="deleteNote(note)" class="w-7 h-7 rounded-lg hover:bg-red-500/20 text-slate-600 hover:text-red-700 flex items-center justify-center transition-colors" title="Hapus Catatan">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Dotted New Note Button -->
            <button @click="openNoteModal()" class="h-64 rounded-3xl border-2 border-dashed border-slate-300 hover:border-amber-500 hover:bg-amber-50/20 transition-all flex flex-col items-center justify-center gap-3 text-slate-500 hover:text-amber-600 font-semibold text-sm group">
                <div class="w-12 h-12 rounded-2xl border border-slate-300 group-hover:border-amber-500 group-hover:bg-amber-50 flex items-center justify-center transition-colors shadow-xs">
                    <i class="fas fa-pen-nib text-lg"></i>
                </div>
                <span>+ Catatan Baru</span>
            </button>
        </div>

        <template x-if="filteredNotes.length === 0">
            <div class="text-center py-12 bg-white rounded-3xl border border-slate-100 shadow-xs mt-4">
                <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-sticky-note text-2xl"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-base">Belum Ada Catatan</h4>
                <p class="text-slate-400 text-xs mt-1">Klik tombol "+ Catatan Baru" untuk menulis ide atau rangkuman baru.</p>
            </div>
        </template>
    </div>

    <!-- MODAL 1: NEW / EDIT NOTE -->
    <div x-show="showNoteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showNoteModal = false"></div>
        <div class="bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 max-w-lg w-full p-6 relative z-10">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fas fa-sticky-note text-amber-500"></i>
                    <span x-text="editNoteForm.id ? 'Edit Catatan' : 'Buat Catatan Baru'"></span>
                </h3>
                <button @click="showNoteModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form @submit.prevent="saveNote()">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Catatan</label>
                        <input type="text" x-model="editNoteForm.title" placeholder="Contoh: Rencana Rapat Evaluasi Mingguan" class="w-full p-3 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 font-bold" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Folder / Kategori</label>
                            <select x-model="editNoteForm.folder_id" class="w-full p-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                                <option value="">Tanpa Folder</option>
                                <template x-for="f in folders" :key="f.id">
                                    <option :value="f.id" x-text="f.name"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal</label>
                            <input type="date" x-model="editNoteForm.note_date" class="w-full p-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Warna Kartu</label>
                        <div class="flex items-center gap-2">
                            <template x-for="c in colorPalette">
                                <button type="button" @click="editNoteForm.color = c" 
                                        :class="editNoteForm.color === c ? 'ring-2 ring-slate-800 scale-110' : 'hover:scale-105'"
                                        class="w-7 h-7 rounded-full border border-black/10 transition-transform" 
                                        :style="'background-color: ' + c"></button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Isi Catatan</label>
                        <textarea x-model="editNoteForm.content" rows="5" placeholder="Tuliskan catatan, rincian ide, atau daftar tugas di sini..." class="w-full p-3 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-amber-500 leading-relaxed"></textarea>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="is_pinned_chk" x-model="editNoteForm.is_pinned" class="form-checkbox h-4 w-4 text-amber-500 rounded border-slate-300 focus:ring-amber-500">
                        <label for="is_pinned_chk" class="text-xs font-semibold text-slate-700 cursor-pointer">Sematkan catatan ini di paling atas (Pin)</label>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showNoteModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm">Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: NEW FOLDER -->
    <div x-show="showFolderModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;" x-cloak>
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showFolderModal = false"></div>
        <div class="bg-white text-slate-800 rounded-3xl shadow-2xl border border-slate-100 max-w-md w-full p-6 relative z-10">
            <div class="flex justify-between items-center mb-4 pb-3 border-b border-slate-100">
                <h3 class="font-extrabold text-lg text-slate-900 flex items-center gap-2">
                    <i class="fas fa-folder-plus text-blue-500"></i> Buat Folder Baru
                </h3>
                <button @click="showFolderModal = false" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-lg"></i></button>
            </div>

            <form @submit.prevent="saveFolder()">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Folder</label>
                        <input type="text" x-model="folderForm.name" placeholder="Contoh: Ide Konten Instagram, Modul Rapat" class="w-full p-3 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 font-bold" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Warna Folder</label>
                        <div class="flex items-center gap-2">
                            <template x-for="c in folderColorPalette">
                                <button type="button" @click="folderForm.color = c" 
                                        :class="folderForm.color === c ? 'ring-2 ring-slate-800 scale-110' : 'hover:scale-105'"
                                        class="w-7 h-7 rounded-full border border-black/10 transition-transform" 
                                        :style="'background-color: ' + c"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" @click="showFolderModal = false" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm">Simpan Folder</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('notesApp', () => ({
            folders: {!! json_encode($folders) !!},
            notes: {!! json_encode($notes) !!},
            selectedFolderId: null,
            timeFilter: 'all',
            searchQuery: '',
            
            showNoteModal: false,
            showFolderModal: false,
            colorPickerNoteId: null,
            folderMenuId: null,
            dragOverFolderId: null,
            draggedNoteId: null,

            colorPalette: ['#FEF3C7', '#FECDD3', '#BAE6FD', '#A7F3D0', '#DDD6FE', '#F1F5F9'],
            folderColorPalette: ['#E0F2FE', '#FFE4E6', '#FEF3C7', '#F3E8FF', '#DCFCE7'],

            editNoteForm: {
                id: null,
                title: '',
                content: '',
                folder_id: '',
                color: '#FEF3C7',
                note_date: new Date().toISOString().split('T')[0],
                is_pinned: false
            },

            folderForm: {
                name: '',
                color: '#E0F2FE'
            },

            get filteredNotes() {
                return this.notes.filter(note => {
                    // Folder Filter
                    if (this.selectedFolderId !== null && note.folder_id != this.selectedFolderId) {
                        return false;
                    }
                    // Search Query Filter
                    if (this.searchQuery.trim() !== '') {
                        let q = this.searchQuery.toLowerCase();
                        let matchTitle = note.title && note.title.toLowerCase().includes(q);
                        let matchContent = note.content && note.content.toLowerCase().includes(q);
                        if (!matchTitle && !matchContent) return false;
                    }
                    // Time Filter
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

            openNoteModal(note = null) {
                if (note) {
                    this.editNoteForm = {
                        id: note.id,
                        title: note.title,
                        content: note.content || '',
                        folder_id: note.folder_id || '',
                        color: note.color || '#FEF3C7',
                        note_date: note.note_date ? note.note_date.split('T')[0] : new Date().toISOString().split('T')[0],
                        is_pinned: !!note.is_pinned
                    };
                } else {
                    this.editNoteForm = {
                        id: null,
                        title: '',
                        content: '',
                        folder_id: this.selectedFolderId || '',
                        color: '#FEF3C7',
                        note_date: new Date().toISOString().split('T')[0],
                        is_pinned: false
                    };
                }
                this.showNoteModal = true;
            },

            openFolderModal() {
                this.folderForm = { name: '', color: '#E0F2FE' };
                this.showFolderModal = true;
            },

            async saveNote() {
                let isEdit = !!this.editNoteForm.id;
                let url = isEdit ? `/admin/notes/${this.editNoteForm.id}` : '/admin/notes';
                let method = isEdit ? 'PUT' : 'POST';

                try {
                    let res = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(this.editNoteForm)
                    });
                    let data = await res.json();
                    if (res.ok && data.success) {
                        if (isEdit) {
                            let idx = this.notes.findIndex(n => n.id === data.note.id);
                            if (idx !== -1) this.notes[idx] = data.note;
                        } else {
                            this.notes.unshift(data.note);
                        }
                        this.showNoteModal = false;
                    }
                } catch (e) {
                    alert('Gagal menyimpan catatan.');
                }
            },

            async deleteNote(note) {
                if (!confirm('Apakah Anda yakin ingin menghapus catatan ini?')) return;
                try {
                    let res = await fetch(`/admin/notes/${note.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    if (res.ok) {
                        this.notes = this.notes.filter(n => n.id !== note.id);
                    }
                } catch (e) {
                    alert('Gagal menghapus catatan.');
                }
            },

            async saveFolder() {
                try {
                    let res = await fetch('/admin/notes/folders', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify(this.folderForm)
                    });
                    let data = await res.json();
                    if (res.ok && data.success) {
                        this.folders.push(data.folder);
                        this.showFolderModal = false;
                    }
                } catch (e) {
                    alert('Gagal menyimpan folder.');
                }
            },

            async deleteFolder(folder) {
                if (!confirm(`Hapus folder "${folder.name}"? Catatan di dalamnya tidak akan terhapus.`)) return;
                try {
                    let res = await fetch(`/admin/notes/folders/${folder.id}`, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                    if (res.ok) {
                        this.folders = this.folders.filter(f => f.id !== folder.id);
                        if (this.selectedFolderId === folder.id) this.selectedFolderId = null;
                    }
                } catch (e) {
                    alert('Gagal menghapus folder.');
                }
            },

            async togglePin(note) {
                note.is_pinned = !note.is_pinned;
                try {
                    await fetch(`/admin/notes/${note.id}/toggle-pin`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        }
                    });
                } catch (e) {}
            },

            async changeNoteColor(note, color) {
                note.color = color;
                this.colorPickerNoteId = null;
                try {
                    await fetch(`/admin/notes/${note.id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            title: note.title,
                            content: note.content,
                            color: color
                        })
                    });
                } catch (e) {}
            },

            // Drag and Drop Note to Folder
            handleNoteDragStart(e, note) {
                this.draggedNoteId = note.id;
                if (e.dataTransfer) {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', note.id);
                }
            },

            handleNoteDragOver(e, note) {
                if (!this.draggedNoteId || this.draggedNoteId === note.id) return;
                e.preventDefault();
            },

            async dropNoteToFolder(folderId) {
                let noteId = this.draggedNoteId;
                this.dragOverFolderId = null;
                this.draggedNoteId = null;

                if (!noteId) return;
                let note = this.notes.find(n => n.id == noteId);
                if (!note) return;

                note.folder_id = folderId;
                note.folder = this.folders.find(f => f.id == folderId) || null;

                try {
                    await fetch(`/admin/notes/${noteId}/move`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ folder_id: folderId })
                    });
                } catch (e) {}
            },

            async handleNoteDrop(e, targetNote) {
                let draggedId = this.draggedNoteId;
                this.draggedNoteId = null;
                if (!draggedId || draggedId === targetNote.id) return;

                let srcIdx = this.notes.findIndex(n => n.id == draggedId);
                let targetIdx = this.notes.findIndex(n => n.id == targetNote.id);

                if (srcIdx !== -1 && targetIdx !== -1) {
                    let [moved] = this.notes.splice(srcIdx, 1);
                    this.notes.splice(targetIdx, 0, moved);

                    // Sync Order
                    let reorderData = this.notes.map((n, idx) => ({ id: n.id, order: idx }));
                    try {
                        await fetch('/admin/notes/reorder', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                            },
                            body: JSON.stringify({ notes: reorderData })
                        });
                    } catch (e) {}
                }
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
