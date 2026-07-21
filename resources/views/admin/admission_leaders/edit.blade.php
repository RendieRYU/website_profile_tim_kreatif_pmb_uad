@extends('layouts.admin')
@section('content')
<h1 class="text-3xl font-bold text-slate-800 mb-6">Edit Pimpinan Admisi</h1>

<div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 max-w-2xl">
    <form action="{{ route('admin.admission-leaders.update', $leader->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <input type="hidden" name="cropped_photo" id="cropped_photo">

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('name') border-red-500 @enderror" value="{{ old('name', $leader->name) }}" required>
            @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Jabatan</label>
            <input type="text" name="position" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('position') border-red-500 @enderror" value="{{ old('position', $leader->position) }}" placeholder="Contoh: Kepala Admisi UAD" required>
            @error('position') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-slate-700 mb-1">Urutan Tampilan</label>
            <input type="number" name="order" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('order') border-red-500 @enderror" value="{{ old('order', $leader->order) }}" placeholder="Angka lebih kecil tampil lebih dulu" required>
            @error('order') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 mb-1">Foto Profil (Rasio 3:4)</label>
            
            <div class="flex items-start gap-4 mb-3">
                @if($leader->photo)
                    <div class="shrink-0">
                        <p class="text-xs text-slate-500 mb-1">Foto Saat Ini:</p>
                        <img src="{{ asset('storage/'.$leader->photo) }}" class="w-24 aspect-[3/4] object-cover rounded-lg border border-slate-200 shadow-sm">
                    </div>
                @endif
                
                <div class="flex-1">
                    <p class="text-xs text-slate-500 mb-1">Ganti Foto:</p>
                    <input type="file" id="photo" class="w-full px-4 py-2 border border-slate-200 rounded-lg focus:outline-none focus:border-blue-500 @error('photo') border-red-500 @enderror">
                </div>
            </div>
            @error('photo') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
            
            <div id="preview_container" class="mt-4 hidden">
                <p class="text-sm font-medium text-slate-500 mb-2">Pratinjau Foto Baru (Hasil Crop):</p>
                <img id="preview_image" src="" class="w-32 aspect-[3/4] object-cover rounded-lg border border-slate-200 shadow-sm">
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.admission-leaders.index') }}" class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 text-slate-700">Batal</a>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Perbarui</button>
        </div>
    </form>
</div>

<!-- Cropper Modal -->
<div id="cropper_modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-800">Sesuaikan Foto Pimpinan (3:4)</h3>
            <button type="button" id="close_modal" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times text-xl"></i></button>
        </div>
        <div class="p-6 flex justify-center items-center max-h-[50vh] bg-slate-50">
            <img id="cropper_image" src="" class="max-w-full block">
        </div>
        <div class="p-6 border-t border-slate-100 flex justify-end space-x-3">
            <button type="button" id="cancel_crop" class="px-4 py-2 border border-slate-200 rounded-lg text-slate-700 hover:bg-slate-50">Batal</button>
            <button type="button" id="apply_crop" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Potong & Terapkan</button>
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
        const photoInput = document.getElementById('photo');
        const croppedPhotoInput = document.getElementById('cropped_photo');
        const previewContainer = document.getElementById('preview_container');
        const previewImage = document.getElementById('preview_image');
        
        const cropperModal = document.getElementById('cropper_modal');
        const cropperImage = document.getElementById('cropper_image');
        const closeBtn = document.getElementById('close_modal');
        const cancelBtn = document.getElementById('cancel_crop');
        const cropBtn = document.getElementById('apply_crop');
        
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
