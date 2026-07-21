@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Tambah Anggota</h1>
<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-4xl">
    <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Panggilan (Opsional)</label>
                <input type="text" name="nickname" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Divisi</label>
                <select name="division_id" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Pilih Divisi --</option>
                    @foreach($divisions as $division)
                        <option value="{{ $division->id }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Periode Aktif</label>
                <select name="period_id" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="">-- Pilih Periode --</option>
                    @foreach($periods as $period)
                        <option value="{{ $period->id }}">{{ $period->name }} {{ $period->is_active ? '(Aktif)' : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Peran / Jabatan (Opsional)</label>
                <input type="text" name="role" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Foto Profile (Rasio 3x4)</label>
                <input type="file" id="photo_input" class="w-full p-2 border border-slate-300 rounded-lg text-sm" accept="image/*">
                <input type="hidden" name="cropped_photo" id="cropped_photo_input">
                <div class="mt-4 hidden" id="preview_container">
                    <p class="text-xs text-slate-500 mb-2">Preview Hasil Crop:</p>
                    <img id="preview_image" src="" class="w-32 h-auto rounded shadow-sm border border-slate-200">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Instagram (Link)</label>
                <input type="url" name="instagram" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">LinkedIn (Link)</label>
                <input type="url" name="linkedin" class="w-full p-2 border border-slate-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>
        <div class="mt-8">
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Simpan</button>
            <a href="{{ route('admin.members.index') }}" class="px-6 py-2 text-slate-600 hover:text-slate-800 ml-4">Batal</a>
        </div>
    </form>
</div>

<!-- Cropper Modal -->
<div id="cropper_modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="text-lg font-bold text-slate-800">Crop Foto Profil (3x4)</h3>
            <button type="button" id="close_modal_btn" class="text-slate-500 hover:text-slate-700"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-4 flex-1 overflow-hidden bg-slate-900 flex items-center justify-center min-h-[300px]">
            <div class="w-full max-w-md">
                <img id="cropper_image" src="" class="max-w-full block">
            </div>
        </div>
        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
            <button type="button" id="cancel_crop_btn" class="px-4 py-2 text-slate-600 hover:bg-slate-200 rounded-lg transition-colors">Batal</button>
            <button type="button" id="crop_btn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">Gunakan Foto</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const photoInput = document.getElementById('photo_input');
        const cropperModal = document.getElementById('cropper_modal');
        const cropperImage = document.getElementById('cropper_image');
        const closeBtn = document.getElementById('close_modal_btn');
        const cancelBtn = document.getElementById('cancel_crop_btn');
        const cropBtn = document.getElementById('crop_btn');
        
        const previewContainer = document.getElementById('preview_container');
        const previewImage = document.getElementById('preview_image');
        const croppedPhotoInput = document.getElementById('cropped_photo_input');

        let cropper;

        photoInput.addEventListener('change', function (e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    cropperImage.src = event.target.result;
                    cropperModal.classList.remove('hidden');
                    
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    cropper = new Cropper(cropperImage, {
                        aspectRatio: 3 / 4,
                        viewMode: 2,
                        background: false,
                    });
                };
                reader.readAsDataURL(files[0]);
            }
        });

        const closeModal = () => {
            cropperModal.classList.add('hidden');
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            if (!croppedPhotoInput.value) {
                photoInput.value = '';
            }
        };

        closeBtn.addEventListener('click', closeModal);
        cancelBtn.addEventListener('click', closeModal);

        cropBtn.addEventListener('click', function () {
            if (cropper) {
                const canvas = cropper.getCroppedCanvas({
                    width: 600,
                    height: 800,
                    imageSmoothingEnabled: true,
                    imageSmoothingQuality: 'high',
                });
                
                const base64Data = canvas.toDataURL('image/jpeg', 0.9);
                
                previewImage.src = base64Data;
                previewContainer.classList.remove('hidden');
                
                croppedPhotoInput.value = base64Data;
                
                closeModal();
            }
        });
    });
</script>
@endpush
