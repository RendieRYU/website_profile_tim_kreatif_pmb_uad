<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Tim Kreatif PMB UAD')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1D4ED8', // Blue-700
                        secondary: '#3B82F6', // Blue-500
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FullCalendar CSS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
        .animate-fade-in { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 glass shadow-sm" x-data="{ open: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <!-- Use Logo if available, otherwise text -->
                        @if(\App\Models\Setting::get('site_logo'))
                            <img src="{{ asset('storage/' . \App\Models\Setting::get('site_logo')) }}" alt="Logo" class="h-10 w-auto object-contain">
                        @else
                            <div class="w-10 h-10 bg-primary text-white rounded-full flex items-center justify-center font-bold text-xl shadow-lg">
                                TK
                            </div>
                        @endif
                        <span class="font-bold text-xl text-primary tracking-tight">Kreatif PMB UAD</span>
                    </a>
                </div>
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="text-slate-600 hover:text-primary font-medium transition-colors">Dashboard</a>
                    <a href="{{ route('about') }}" class="text-slate-600 hover:text-primary font-medium transition-colors">Tentang Kami</a>
                    <a href="{{ route('portfolio.index') }}" class="text-slate-600 hover:text-primary font-medium transition-colors">Portofolio</a>
                    @auth
                        <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-primary text-white rounded-lg font-medium hover:bg-blue-800 transition-all hover:shadow-lg">Admin Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 border-2 border-primary text-primary rounded-lg font-medium hover:bg-primary hover:text-white transition-all">Login Admin</a>
                    @endauth
                </div>
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="text-slate-600 hover:text-primary focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div x-show="open" class="md:hidden bg-white border-t" x-transition>
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="{{ route('home') }}" class="block px-3 py-2 text-slate-600 font-medium hover:bg-slate-100 rounded-md">Dashboard</a>
                <a href="{{ route('about') }}" class="block px-3 py-2 text-slate-600 font-medium hover:bg-slate-100 rounded-md">Tentang Kami</a>
                <a href="{{ route('portfolio.index') }}" class="block px-3 py-2 text-slate-600 font-medium hover:bg-slate-100 rounded-md">Portofolio</a>
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 text-primary font-bold hover:bg-slate-100 rounded-md">Admin Panel</a>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 text-primary font-bold hover:bg-slate-100 rounded-md">Login Admin</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pt-20 animate-fade-in">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-xl font-bold text-white mb-4">Tim Kreatif PMB UAD</h3>
            <div class="flex justify-center gap-6 mb-4">
                @if(\App\Models\Setting::get('instagram_link'))
                <a href="{{ \App\Models\Setting::get('instagram_link') }}" target="_blank" class="text-2xl hover:text-pink-500 transition-colors"><i class="fab fa-instagram"></i></a>
                @endif
                @if(\App\Models\Setting::get('tiktok_link'))
                <a href="{{ \App\Models\Setting::get('tiktok_link') }}" target="_blank" class="text-2xl hover:text-white transition-colors"><i class="fab fa-tiktok"></i></a>
                @endif
            </div>
            <p class="text-sm opacity-75">&copy; {{ date('Y') }} Tim Kreatif PMB UAD. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
