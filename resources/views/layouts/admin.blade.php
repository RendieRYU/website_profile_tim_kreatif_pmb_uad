<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tim Kreatif PMB UAD</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 antialiased flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-slate-900 text-white min-h-screen flex flex-col fixed md:relative z-20 transition-transform transform -translate-x-full md:translate-x-0" id="sidebar" x-data="{ open: true }" :class="{'translate-x-0': open}">
        <div class="p-6 flex items-center justify-between">
            <span class="text-xl font-bold tracking-tight text-white">TK Admin</span>
            <button class="md:hidden text-slate-400 hover:text-white" @click="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <nav class="flex-1 px-4 space-y-2 mt-4">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            <a href="{{ route('admin.divisions.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.divisions.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-layer-group w-5"></i> Divisi
            </a>
            <a href="{{ route('admin.periods.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.periods.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-calendar-alt w-5"></i> Periode
            </a>
            <a href="{{ route('admin.members.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.members.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-users w-5"></i> Anggota
            </a>
            <a href="{{ route('admin.events.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.events.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-calendar-check w-5"></i> Kegiatan
            </a>
            <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-tags w-5"></i> Kategori Kegiatan
            </a>
            <a href="{{ route('admin.news.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.news.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-newspaper w-5"></i> Berita/Portofolio
            </a>
            <a href="{{ route('admin.metrics.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.metrics.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-chart-line w-5"></i> Statistik & Metrik
            </a>
            <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-slate-800 transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                <i class="fas fa-cog w-5"></i> Pengaturan
            </a>
        </nav>
        <div class="p-4 border-t border-slate-700">
            <a href="{{ route('admin.account.edit') }}" class="flex items-center gap-3 px-4 py-2 text-slate-300 hover:text-white transition-colors">
                <i class="fas fa-user-circle w-5"></i> Akun Saya
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-left text-red-400 hover:text-red-300 hover:bg-slate-800 rounded-lg transition-colors">
                    <i class="fas fa-sign-out-alt w-5"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen relative w-full md:w-auto">
        <!-- Top Navbar for Mobile -->
        <header class="bg-white shadow-sm md:hidden p-4 flex justify-between items-center z-10">
            <div class="flex items-center gap-2">
                <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="text-slate-600 hover:text-blue-600 focus:outline-none text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
                <span class="font-bold text-lg text-slate-800">Admin Panel</span>
            </div>
        </header>

        <!-- Content Area -->
        <main class="flex-1 p-4 md:p-8 overflow-y-auto">
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
</body>
</html>
