@php
    $isServerUnlocked = auth()->check() || session('public_access_unlocked', false);
@endphp

<div x-data="publicAccessGuard({{ $isServerUnlocked ? 'true' : 'false' }})" class="relative">
    <!-- Main Content Wrapped (Blurred when locked) -->
    <div :class="{ 'filter blur-lg select-none pointer-events-none opacity-40 transition-all duration-500': !isUnlocked }">
        {{ $slot }}
    </div>

    <!-- Floating Unlock Modal Overlay (Visible when locked) -->
    <div x-show="!isUnlocked" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         class="fixed inset-0 z-40 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md"
         x-cloak>
        <div class="bg-white/90 backdrop-blur-xl border border-white/40 shadow-2xl rounded-3xl p-8 max-w-md w-full text-center relative overflow-hidden" @click.outside="false">
            <!-- Top Gradient Accent -->
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-blue-600 via-indigo-500 to-purple-600"></div>

            <!-- Lock Icon -->
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center mx-auto mb-5 shadow-lg shadow-blue-500/30 animate-bounce">
                <i class="fas fa-lock text-2xl"></i>
            </div>

            <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Halaman Terkunci</h3>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed mb-6">
                Masukkan kode akses / password untuk membuka kalender, papan projek, dan catatan tim. <br>
                <span class="text-blue-600 font-semibold">(Cukup masukkan 1x untuk seluruh halaman)</span>
            </p>

            <form @submit.prevent="submitPin()">
                <div class="space-y-4">
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                               x-model="pinInput" 
                               placeholder="Masukkan Kode PIN..." 
                               class="w-full text-center text-lg font-bold tracking-widest px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-blue-600 focus:border-blue-600 placeholder:text-slate-300 placeholder:font-normal placeholder:tracking-normal placeholder:text-sm" 
                               required autofocus>
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                        </button>
                    </div>

                    <template x-if="errorMessage">
                        <p class="text-xs text-red-600 font-bold flex items-center justify-center gap-1.5" x-text="errorMessage"></p>
                    </template>

                    <button type="submit" 
                            :disabled="isLoading"
                            class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-sm rounded-2xl shadow-lg shadow-blue-600/30 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                        <template x-if="!isLoading">
                            <span class="flex items-center gap-2"><i class="fas fa-key"></i> Buka Akses Halaman</span>
                        </template>
                        <template x-if="isLoading">
                            <span class="flex items-center gap-2"><i class="fas fa-spinner fa-spin"></i> Memverifikasi...</span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('publicAccessGuard', (initialServerState) => ({
            isUnlocked: initialServerState || localStorage.getItem('public_access_unlocked') === 'true',
            pinInput: '',
            showPassword: false,
            errorMessage: '',
            isLoading: false,

            async submitPin() {
                this.errorMessage = '';
                this.isLoading = true;

                try {
                    let res = await fetch('{{ route("api.verify-public-pin") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({ pin: this.pinInput })
                    });

                    let data = await res.json();
                    if (res.ok && data.success) {
                        this.isUnlocked = true;
                        localStorage.setItem('public_access_unlocked', 'true');
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message || 'Akses berhasil dibuka!',
                                showConfirmButton: false,
                                timer: 2500
                            });
                        }
                    } else {
                        this.errorMessage = data.message || 'Kode akses tidak cocok.';
                    }
                } catch (e) {
                    this.errorMessage = 'Terjadi kesalahan koneksi. Silakan coba lagi.';
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    });
</script>
@endpush
