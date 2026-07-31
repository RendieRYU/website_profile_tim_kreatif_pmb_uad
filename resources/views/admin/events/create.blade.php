@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Tambah Kegiatan</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.events.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Kegiatan</label>
                <input type="text" name="title" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Kegiatan</label>
                <textarea name="description" rows="4" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
                <input type="date" name="event_date" value="{{ old('event_date', $selectedDate ?? date('Y-m-d')) }}" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Waktu (Opsional)</label>
                <input type="time" name="event_time" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-1">Status</label>
                <select name="status" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="ide">Ide</option>
                    <option value="acc">ACC</option>
                    <option value="terjadwal">Terjadwal</option>
                    <option value="ditunda">Ditunda</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Kategori Kegiatan</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 p-4 border border-slate-200 rounded-lg bg-slate-50">
                    @foreach($categories as $category)
                    <label class="flex items-center space-x-3 bg-white p-2 border border-slate-100 rounded shadow-sm hover:bg-blue-50 cursor-pointer transition-colors">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="form-checkbox h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                        <span class="text-slate-700 text-sm font-medium">
                            <span class="inline-block w-3 h-3 rounded-full mr-1" style="background-color: {{ $category->color }}"></span>
                            {{ $category->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="md:col-span-2" x-data="{ 
                links: [{ title: '', url: '' }],
                addLink() { this.links.push({ title: '', url: '' }); },
                removeLink(index) { if(this.links.length > 1) { this.links.splice(index, 1); } else { this.links[0] = { title: '', url: '' }; } }
            }">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-medium text-slate-700">Tautan Kegiatan / Media Sosial (Bisa lebih dari 1)</label>
                    <button type="button" @click="addLink()" class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1 rounded-md font-semibold transition-colors flex items-center gap-1"><i class="fas fa-plus"></i> Tambah Tautan</button>
                </div>
                <div class="space-y-3 p-4 border border-slate-200 rounded-lg bg-slate-50">
                    <template x-for="(item, index) in links" :key="index">
                        <div class="flex items-center gap-2">
                            <input type="text" :name="'links['+index+'][title]'" x-model="item.title" placeholder="Label/Judul (misal: IG, TikTok, Drive)" class="w-1/3 p-2 text-sm border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <input type="url" :name="'links['+index+'][url]'" x-model="item.url" placeholder="https://..." class="flex-1 p-2 text-sm border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <button type="button" @click="removeLink(index)" class="p-2 text-red-500 hover:text-red-700 transition-colors" title="Hapus"><i class="fas fa-trash-alt"></i></button>
                        </div>
                    </template>
                </div>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-slate-700 mb-2">Petugas Bertugas</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 max-h-60 overflow-y-auto p-4 border border-slate-200 rounded-lg bg-slate-50">
                    @foreach($members as $member)
                    <div class="bg-white p-3 border border-slate-100 rounded shadow-sm transition-colors hover:bg-blue-50">
                        <label class="flex items-center space-x-3 cursor-pointer mb-2">
                            <input type="checkbox" name="members[]" value="{{ $member->id }}" 
                                class="form-checkbox h-4 w-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                                onchange="document.getElementById('role-{{ $member->id }}').disabled = !this.checked">
                            <span class="text-slate-700 text-sm font-medium">{{ $member->full_name }} <span class="text-xs text-slate-400">({{ $member->division->name ?? '-' }})</span></span>
                        </label>
                        <select name="member_roles[{{ $member->id }}]" id="role-{{ $member->id }}" disabled class="w-full text-sm p-1.5 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 disabled:bg-slate-100 disabled:text-slate-400">
                            <option value="">Pilih Pekerjaan...</option>
                            <option value="Host">Host</option>
                            <option value="MC">MC</option>
                            <option value="FG">FG</option>
                            <option value="CC">CC</option>
                            <option value="BC">BC</option>
                            <option value="DG">DG (Desain Grafis)</option>
                            <option value="Admin">Admin</option>
                            <option value="Lainnya">Lainnya...</option>
                        </select>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="mt-8">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            <a href="{{ route('admin.events.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
        </div>
    </form>
</div>
@endsection
