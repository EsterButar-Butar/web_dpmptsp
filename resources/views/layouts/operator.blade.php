<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Operator</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/data-wilayah.js') }}"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://unpkg.com/htmx.org@2.0.0"></script>
</head>
<body class="antialiased bg-slate-50 text-slate-900" hx-boost="true">
    <div class="min-h-screen flex">
        
        <!-- Sidebar -->
        <aside class="w-64 flex-shrink-0 bg-gradient-to-b from-[#145239] to-[#0F8A5F] text-white min-h-screen shadow-xl transition-all duration-300 relative z-20">
            <!-- Logo Section -->
            <div class="p-6 pb-2 flex justify-center">
                <img src="{{ asset('images/logo-dpmptsp.png') }}" alt="Logo DPMPTSP Sumut" class="w-48 h-auto object-contain" onerror="this.src='https://ui-avatars.com/api/?name=DPMPTSP&color=fff&background=145239'">
            </div>

            <!-- Profile Section with Dropdown -->
            <div x-data="{ open: false }" class="border-b border-[#CFE3D5]/20 mb-4 relative">
                <button @click="open = !open" class="w-full px-6 py-4 flex items-center justify-between hover:bg-[#1E5D41]/50 transition-colors focus:outline-none text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-[#FFD54F] overflow-hidden shadow-inner flex-shrink-0 border-2 border-[#E7F2EB]">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()?->name ?? 'Siti') }}&background=FFD54F&color=145239" alt="Profile" class="w-full h-full object-cover">
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-semibold text-sm truncate text-white">{{ Auth::user()?->name ?? 'Siti' }}</h3>
                            <p class="text-xs text-[#FFD54F] truncate">{{ Auth::user()?->role ?? 'Operator' }}</p>
                        </div>
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 text-[#FFD54F] transition-transform duration-200 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="open" x-transition.opacity.duration.200ms x-cloak class="bg-[#145239] px-4 py-2 space-y-1 shadow-inner border-b border-[#CFE3D5]/20">
                    <a href="{{ route('operator.profile') }}" class="flex items-center gap-3 px-2 py-2 text-sm font-medium hover:text-white hover:bg-[#1E5D41] rounded-lg transition-colors {{ request()->routeIs('operator.profile') ? 'text-[#145239] bg-[#FFD54F]' : 'text-[#E7F2EB]' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('operator.profile') ? 'text-[#145239]' : 'text-[#FFD54F]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Profile
                    </a>
                    <a href="{{ route('operator.settings') }}" class="flex items-center gap-3 px-2 py-2 text-sm font-medium hover:text-white hover:bg-[#1E5D41] rounded-lg transition-colors {{ request()->routeIs('operator.settings') ? 'text-[#145239] bg-[#FFD54F]' : 'text-[#E7F2EB]' }}">
                        <svg class="w-4 h-4 {{ request()->routeIs('operator.settings') ? 'text-[#145239]' : 'text-[#FFD54F]' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                    
                    <form method="POST" action="#" onsubmit="event.preventDefault(); alert('Ini versi simulasi logout.')" class="mt-2 border-t border-[#CFE3D5]/20 pt-2">
                        @csrf
                        <button type="submit" class="w-full flex items-center gap-3 px-2 py-2 text-sm font-medium text-red-300 hover:text-red-100 hover:bg-red-900/60 rounded-lg transition-colors">
                            <svg class="w-4 h-4 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="px-3 space-y-1">
                <div class="px-3 mb-2 text-xs font-semibold text-[#FFD54F] uppercase tracking-wider">Menu Operator</div>
                
                <a href="{{ route('operator.dashboard') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('operator.dashboard') ? 'bg-[#FFD54F] text-[#145239] shadow-sm' : 'text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('operator.dashboard') ? 'text-[#145239]' : 'text-[#CFE3D5] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    Dashboard Operator
                </a>

                <a href="{{ route('operator.lq.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('operator.lq.index') ? 'bg-[#FFD54F] text-[#145239] shadow-sm' : 'text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('operator.lq.index') ? 'text-[#145239]' : 'text-[#CFE3D5] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Analisis LQ
                </a>

                <a href="{{ route('operator.ss.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('operator.ss.index') ? 'bg-[#FFD54F] text-[#145239] shadow-sm' : 'text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('operator.ss.index') ? 'text-[#145239]' : 'text-[#CFE3D5] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                    </svg>
                    Analisis SS
                </a>

                <a href="{{ route('operator.tipologi.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('operator.tipologi.index') ? 'bg-[#FFD54F] text-[#145239] shadow-sm' : 'text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('operator.tipologi.index') ? 'text-[#145239]' : 'text-[#CFE3D5] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                    </svg>
                    Analisis Tipologi Sektor
                </a>

                <a href="{{ route('operator.klassen.index') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-all {{ request()->routeIs('operator.klassen.index') ? 'bg-[#FFD54F] text-[#145239] shadow-sm' : 'text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 {{ request()->routeIs('operator.klassen.index') ? 'text-[#145239]' : 'text-[#CFE3D5] group-hover:text-white' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    Analisis Klassen
                </a>

                <div class="px-3 mt-6 mb-2 text-xs font-semibold text-[#FFD54F] uppercase tracking-wider">Menu Utama</div>
                
                <a href="{{ url('/') }}" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg text-[#E7F2EB] hover:bg-[#1E5D41] hover:text-white transition-all">
                    <svg class="w-5 h-5 mr-3 text-[#CFE3D5] group-hover:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Beranda
                </a>

            </nav>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen overflow-y-auto relative">
            <div class="p-4 md:p-6 lg:p-8 w-full space-y-6">
                @yield('content')
            </div>
        </main>
        
    </div>
</body>
</html>
