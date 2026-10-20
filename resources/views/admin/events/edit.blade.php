@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('admin.events.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <i class="fas fa-arrow-left"></i> Kembali ke Kalender
        </a>
    </div>

    <!-- Notion Style Page Container -->
    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 md:p-10 space-y-8 relative overflow-hidden">
        <form action="{{ route('admin.events.update', $event) }}" method="POST">
            @csrf @method('PUT')

            <!-- Header Action Controls -->
            <div class="flex items-center justify-between pb-6 border-b border-slate-100 mb-6 text-xs text-slate-400 font-mono">
                <span class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span>Edit Kegiatan #{{ $event->id }}</span>
                </span>
                <span>Terakhir diubah: {{ $event->updated_at->diffForHumans() }}</span>
            </div>

            <!-- Notion Page Title (Borderless Heading) -->
            <div class="mb-8">
                <input type="text" name="title" value="{{ old('title', $event->title) }}" placeholder="Judul Kegiatan..." 
                       class="w-full text-3xl md:text-4xl font-extrabold text-slate-900 placeholder-slate-300 border-none focus:outline-none focus:ring-0 p-0 tracking-tight" 
                       required>
            </div>

            <!-- Properties List (Notion Style) -->
            <div class="space-y-5 text-sm mb-10">
                <h3 class="text-xs font-extrabold tracking-wider uppercase text-slate-400 mb-4">Properti Kegiatan (Properties)</h3>

                <!-- Property: Tanggal & Waktu (Due Date) -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-2 md:gap-4 py-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                        <i class="far fa-calendar-alt w-5 text-center text-blue-500"></i>
                        <span>Tanggal & Waktu</span>
                    </div>
                    <div class="md:col-span-3 flex flex-col sm:flex-row gap-3">
                        <input type="date" name="event_date" value="{{ old('event_date', $event->event_date->format('Y-m-d')) }}" 
                               class="p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500" required>
                        <input type="time" name="event_time" value="{{ old('event_time', $event->event_time ? $event->event_time->format('H:i') : '') }}" 
                               class="p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:ring-2 focus:ring-blue-500" placeholder="Jam Pelaksanaan">
                    </div>
                </div>

                <!-- Property: Status -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-center gap-2 md:gap-4 py-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium">
                        <i class="fas fa-dot-circle w-5 text-center text-amber-500"></i>
                        <span>Status</span>
                    </div>
                    <div class="md:col-span-3">
                        <select name="status" class="p-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-blue-500 w-full sm:w-48" required>
                            <option value="ide" {{ $event->status == 'ide' ? 'selected' : '' }}>Ide</option>
                            <option value="acc" {{ $event->status == 'acc' ? 'selected' : '' }}>ACC (On-Going)</option>
                            <option value="terjadwal" {{ $event->status == 'terjadwal' ? 'selected' : '' }}>Terjadwal (To-Do)</option>
                            <option value="ditunda" {{ $event->status == 'ditunda' ? 'selected' : '' }}>Ditunda</option>
                            <option value="selesai" {{ $event->status == 'selesai' ? 'selected' : '' }}>Selesai (Complete)</option>
                            <option value="ditolak" {{ $event->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>
                </div>

                <!-- Property: Tags / Kategori -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-2 md:gap-4 py-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium mt-1">
                        <i class="fas fa-tags w-5 text-center text-emerald-500"></i>
                        <span>Kategori (Tags)</span>
                    </div>
                    <div class="md:col-span-3 flex flex-wrap gap-2">
                        @foreach($categories as $category)
                        <label class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl border border-slate-200 bg-slate-50 hover:bg-blue-50 cursor-pointer transition-colors text-xs font-semibold text-slate-700">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}" {{ in_array($category->id, $event->categories->pluck('id')->toArray()) ? 'checked' : '' }} class="form-checkbox h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                            <span class="inline-block w-2.5 h-2.5 rounded-full" style="background-color: {{ $category->color }}"></span>
                            <span>{{ $category->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Property: Petugas Bertugas -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-2 md:gap-4 py-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium mt-1">
                        <i class="fas fa-users w-5 text-center text-purple-500"></i>
                        <span>Petugas (Members)</span>
                    </div>
                    <div class="md:col-span-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 max-h-56 overflow-y-auto p-3 border border-slate-200 rounded-2xl bg-slate-50">
                            @foreach($members as $member)
                            @php 
                                $isAttached = $event->members->contains('id', $member->id);
                                $currentRole = $isAttached ? $event->members->where('id', $member->id)->first()->pivot->role : '';
                                $isStandardRole = in_array($currentRole, ['Host', 'MC', 'FG', 'CC', 'BC', 'DG', 'Admin']);
                                $initialSelectRole = $isStandardRole ? $currentRole : ($currentRole ? 'Lainnya' : '');
                                $initialCustomRole = !$isStandardRole ? $currentRole : '';
                            @endphp
                            <div class="bg-white p-2.5 border border-slate-100 rounded-xl shadow-2xs space-y-1.5"
                                 x-data="{ isChecked: {{ $isAttached ? 'true' : 'false' }}, selectedRole: '{{ $initialSelectRole }}', customRole: '{{ $initialCustomRole }}' }">
                                <label class="flex items-center space-x-2.5 cursor-pointer mb-1">
                                    <input type="checkbox" name="members[]" value="{{ $member->id }}" 
                                        x-model="isChecked"
                                        class="form-checkbox h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                                    <span class="text-slate-800 text-xs font-bold">{{ $member->full_name }} <span class="text-[10px] text-slate-400 font-normal">({{ $member->division->name ?? '-' }})</span></span>
                                </label>
                                <select name="member_roles[{{ $member->id }}]" 
                                        x-model="selectedRole"
                                        :disabled="!isChecked" 
                                        class="w-full text-xs p-1.5 border border-slate-200 rounded-lg focus:ring-blue-500 disabled:bg-slate-100 disabled:text-slate-400 font-medium">
                                    <option value="">Pilih Peran Pekerjaan...</option>
                                    <option value="Host">Host</option>
                                    <option value="MC">MC</option>
                                    <option value="FG">FG</option>
                                    <option value="CC">CC</option>
                                    <option value="BC">BC</option>
                                    <option value="DG">DG</option>
                                    <option value="Admin">Admin</option>
                                    <option value="Lainnya">Lainnya...</option>
                                </select>
                                <input type="text" name="member_custom_roles[{{ $member->id }}]"
                                       x-show="selectedRole === 'Lainnya' && isChecked"
                                       x-model="customRole"
                                       placeholder="Ketik nama pekerjaan baru..."
                                       :disabled="!isChecked"
                                       class="w-full text-xs p-1.5 border border-amber-300 bg-amber-50/70 rounded-lg focus:ring-2 focus:ring-amber-500 font-semibold text-slate-800 placeholder-slate-400" x-cloak>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Property: Tautan Multi-Link -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-2 md:gap-4 py-2 border-b border-slate-100" x-data="{ 
                    links: {{ json_encode(!empty($event->links) ? $event->links : ($event->link ? [['title' => 'Tautan Utama', 'url' => $event->link]] : [['title' => '', 'url' => '']])) }},
                    addLink() { this.links.push({ title: '', url: '' }); },
                    removeLink(index) { if(this.links.length > 1) { this.links.splice(index, 1); } else { this.links[0] = { title: '', url: '' }; } }
                }">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium mt-1">
                        <i class="fas fa-link w-5 text-center text-pink-500"></i>
                        <span>Tautan (URLs)</span>
                    </div>
                    <div class="md:col-span-3 space-y-2">
                        <template x-for="(item, index) in links" :key="index">
                            <div class="flex items-center gap-2">
                                <input type="text" :name="'links['+index+'][title]'" x-model="item.title" placeholder="Judul Tautan (misal: Drive, IG)" class="w-1/3 p-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <input type="url" :name="'links['+index+'][url]'" x-model="item.url" placeholder="https://..." class="flex-1 p-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500">
                                <button type="button" @click="removeLink(index)" class="p-2 text-red-500 hover:text-red-700 transition-colors" title="Hapus"><i class="fas fa-trash-alt text-xs"></i></button>
                            </div>
                        </template>
                        <button type="button" @click="addLink()" class="text-xs bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1.5 rounded-xl font-bold transition-colors inline-flex items-center gap-1">
                            <i class="fas fa-plus text-[10px]"></i> Tambah Tautan Baru
                        </button>
                    </div>
                </div>

                <!-- Property: Deskripsi / Content Body -->
                <div class="grid grid-cols-1 md:grid-cols-4 items-start gap-2 md:gap-4 py-2 border-b border-slate-100">
                    <div class="flex items-center gap-2.5 text-slate-500 font-medium mt-1">
                        <i class="fas fa-align-left w-5 text-center text-slate-400"></i>
                        <span>Deskripsi / Catatan</span>
                    </div>
                    <div class="md:col-span-3">
                        <textarea name="description" rows="4" placeholder="Tuliskan deskripsi rincian kegiatan di sini..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs leading-relaxed focus:ring-2 focus:ring-blue-500 font-medium">{{ old('description', $event->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Page Footer Actions -->
            <div class="flex items-center justify-end gap-3 pt-4">
                <a href="{{ route('admin.events.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-semibold transition-colors">Batal</a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-check"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
