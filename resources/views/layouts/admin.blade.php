<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - Tim Kreatif PMB UAD</title>
    <!-- Favicon -->
    @if(\App\Models\Setting::get('site_logo'))
        <link rel="icon" href="{{ asset('storage/' . \App\Models\Setting::get('site_logo')) }}">
    @else
        <link rel="icon" href="{{ asset('favicon.ico') }}">
    @endif
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        html, body {
            font-family: 'Inter', sans-serif;
        }
        .scrollbar-thin::-webkit-scrollbar { width: 6px; height: 6px; }
        .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
        .scrollbar-thin::-webkit-scrollbar-thumb { background: rgba(100,116,139,0.3); border-radius: 4px; }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: rgba(100,116,139,0.5); }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">

    <!-- Sidebar Backdrop Overlay -->
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-20 md:hidden"
         x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
    </div>

    <!-- Sidebar -->
    <aside class="w-60 bg-slate-900 text-white h-screen flex flex-col fixed md:sticky md:top-0 z-30 overflow-y-auto transition-transform duration-300 transform md:translate-x-0 scrollbar-thin shrink-0" id="sidebar" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <!-- Header -->
        <div class="px-4 pt-4 pb-2 flex items-center justify-between">
            <span class="text-base font-bold tracking-tight text-white">Tim Kreatif Admin</span>
            <button class="md:hidden text-slate-400 hover:text-white text-lg" @click="sidebarOpen = false">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Kembali ke Website -->
        <div class="px-3 pb-2">
            <a href="{{ url('/') }}" class="flex items-center gap-2 px-3 py-1.5 text-[11px] font-semibold text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                <i class="fas fa-arrow-left text-[10px]"></i> Kembali ke Website
            </a>
        </div>

        <nav class="flex-1 px-3 space-y-0.5 mt-1">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300' }}">
                <i class="fas fa-home w-4 text-center text-xs"></i> Dashboard
            </a>

            <!-- Personal: Catatan & Projek -->
            <a href="{{ route('admin.notes.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.notes.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300' }}">
                <i class="fas fa-sticky-note w-4 text-center text-xs text-amber-400"></i> Catatan Saya
            </a>
            <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.projects.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300' }}">
                <i class="fas fa-tasks w-4 text-center text-xs text-emerald-400"></i> Projek
            </a>

            @if(auth()->user()->role === 'anggota')
            <a href="{{ route('admin.works.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.works.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300' }}">
                <i class="fas fa-briefcase w-4 text-center text-xs"></i> Karya / Portofolio
            </a>
            @endif

            @if(auth()->user()->role === 'admin')
            <!-- Divider -->
            <div class="border-t border-slate-700/60 my-1.5"></div>

            <!-- Grup: Organisasi -->
            <div x-data="{ open: {{ request()->routeIs('admin.divisions.*', 'admin.periods.*', 'admin.members.*', 'admin.admission-leaders.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] text-slate-400 font-semibold">
                    <span class="flex items-center gap-2.5"><i class="fas fa-building w-4 text-center text-xs"></i> Organisasi</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-0.5 border-l border-slate-800 ml-4 my-0.5">
                    <a href="{{ route('admin.divisions.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.divisions.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Divisi
                    </a>
                    <a href="{{ route('admin.periods.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.periods.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Periode Kepengurusan
                    </a>
                    <a href="{{ route('admin.members.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.members.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Anggota
                    </a>
                    <a href="{{ route('admin.admission-leaders.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.admission-leaders.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Kepala PMB
                    </a>
                </div>
            </div>

            <!-- Grup: Kegiatan -->
            <div x-data="{ open: {{ request()->routeIs('admin.events.*', 'admin.categories.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] text-slate-400 font-semibold">
                    <span class="flex items-center gap-2.5"><i class="fas fa-calendar-alt w-4 text-center text-xs"></i> Kegiatan</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-0.5 border-l border-slate-800 ml-4 my-0.5">
                    <a href="{{ route('admin.events.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.events.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Kalender & Spreadsheet
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.categories.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Kategori Kegiatan
                    </a>
                </div>
            </div>

            <!-- Grup: Konten -->
            <div x-data="{ open: {{ request()->routeIs('admin.news.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] text-slate-400 font-semibold">
                    <span class="flex items-center gap-2.5"><i class="fas fa-pen-nib w-4 text-center text-xs"></i> Konten</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-0.5 border-l border-slate-800 ml-4 my-0.5">
                    <a href="{{ route('admin.news.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.news.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        Berita / Artikel
                    </a>
                </div>
            </div>

            <!-- Grup: Laporan & Statistik Media -->
            <div x-data="{ open: {{ request()->routeIs('admin.metrics.*', 'admin.instagram-posts.*', 'admin.tiktok-posts.*') ? 'true' : 'false' }} }">
                <button @click="open = !open" class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] text-slate-400 font-semibold">
                    <span class="flex items-center gap-2.5"><i class="fas fa-chart-line w-4 text-center text-xs"></i> Laporan</span>
                    <i class="fas fa-chevron-down text-[10px] transition-transform duration-200" :class="open ? 'rotate-180 text-white' : ''"></i>
                </button>
                <div x-show="open" x-collapse class="pl-4 pr-1 py-1 space-y-0.5 border-l border-slate-800 ml-4 my-0.5">
                    <a href="{{ route('admin.metrics.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.metrics.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        <i class="fas fa-chart-area text-[10px]"></i> Statistik & Metrik
                    </a>
                    <a href="{{ route('admin.instagram-posts.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.instagram-posts.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        <i class="fab fa-instagram text-[10px]"></i> Laporan Instagram
                    </a>
                    <a href="{{ route('admin.tiktok-posts.index') }}" class="flex items-center gap-2 px-2.5 py-1.5 rounded-lg hover:bg-slate-800 transition-colors text-xs {{ request()->routeIs('admin.tiktok-posts.*') ? 'text-blue-400 font-bold bg-slate-800/60' : 'text-slate-400' }}">
                        <i class="fab fa-tiktok text-[10px]"></i> Laporan TikTok
                    </a>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-slate-700/60 my-1.5"></div>

            <!-- Pengaturan -->
            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white font-bold' : 'text-slate-300' }}">
                <i class="fas fa-cog w-4 text-center text-xs"></i> Pengaturan
            </a>
            @endif
        </nav>

        <!-- User Profile & Logout Section at Bottom -->
        <div class="p-3 border-t border-slate-800 mt-auto bg-slate-950/40">
            <a href="{{ route('admin.account.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-lg hover:bg-slate-800 transition-colors text-[13px] {{ request()->routeIs('admin.account.*') ? 'text-blue-400 font-bold' : 'text-slate-300' }}">
                <i class="fas fa-user-circle w-4 text-center text-xs"></i> Akun Saya
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex items-center gap-2.5 px-3 py-2 w-full text-left text-red-400 hover:text-red-300 hover:bg-slate-800 rounded-lg transition-colors text-[13px]">
                    <i class="fas fa-sign-out-alt w-4 text-center text-xs"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 min-w-0 flex flex-col h-screen overflow-y-auto relative w-full md:w-auto scrollbar-thin">
        <!-- Top Navbar for Mobile -->
        <header class="bg-white shadow-sm md:hidden p-4 flex justify-between items-center z-10 shrink-0">
            <div class="flex items-center gap-2">
                <button @click="sidebarOpen = !sidebarOpen" class="text-slate-600 hover:text-blue-600 focus:outline-none text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="font-bold text-lg text-slate-800">Admin Panel</span>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 @yield('main_class', 'p-4 md:p-8') min-w-0">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded shadow-sm flex items-start gap-3" x-data="{ show: true }" x-show="show">
                <i class="fas fa-check-circle text-green-500 mt-1"></i>
                <div class="flex-1">
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
                <button @click="show = false" class="text-green-600 hover:text-green-800"><i class="fas fa-times"></i></button>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded shadow-sm flex items-start gap-3" x-data="{ show: true }" x-show="show">
                <i class="fas fa-exclamation-circle text-red-500 mt-1"></i>
                <div class="flex-1">
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
                <button @click="show = false" class="text-red-600 hover:text-red-800"><i class="fas fa-times"></i></button>
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert2 Delete Confirmation
            const deleteForms = document.querySelectorAll('form[onsubmit*="return confirm"]');
            deleteForms.forEach(form => {
                const originalOnsubmit = form.getAttribute('onsubmit');
                // Extract message from confirm('...') if possible
                let message = "Data ini tidak bisa dikembalikan!";
                const match = originalOnsubmit.match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) {
                    message = match[1];
                }
                
                form.removeAttribute('onsubmit');
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444', // red-500
                        cancelButtonColor: '#64748b', // slate-500
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
