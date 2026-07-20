import re

with open('resources/views/layouts/admin.blade.php', 'r', encoding='utf-8') as f:
    admin_html = f.read()

with open('resources/views/partials/layouts/operator.blade.php', 'r', encoding='utf-8') as f:
    operator_html = f.read()

# Extract parts from admin
head_styles_regex = re.compile(r'(<style>.*?</style>)', re.DOTALL)
admin_styles = head_styles_regex.search(admin_html).group(1)

js_regex = re.compile(r'(<script>.*?document\.addEventListener\(\'DOMContentLoaded\'.*?</script>)', re.DOTALL)
admin_js = js_regex.search(admin_html).group(1)

# Extract sweetalert script from operator
sweet_alert_regex = re.compile(r'(<!-- SweetAlert2 -->.*?)</body', re.DOTALL)
operator_scripts = sweet_alert_regex.search(operator_html).group(1)

# Build the new operator layout
new_operator = f'''<!DOCTYPE html>
<html lang="{{{{ str_replace('_', '-', app()->getLocale()) }}}}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{{{ csrf_token() }}}}">

    <title>{{{{ config('app.name', 'Laravel') }}}} - Operator</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{{{ asset('js/data-wilayah.js') }}}}"></script>

    {admin_styles}

    @stack('styles')
</head>
<body>
    <button type="button" id="mobileSidebarButton" class="mobile-sidebar-button" aria-label="Buka menu">
        <i class="fa-solid fa-bars"></i>
    </button>

    <div id="sidebarBackdrop" class="sidebar-backdrop"></div>

    <div class="admin-shell">
        <aside id="adminSidebar" class="admin-sidebar">
            <div class="sidebar-logo">
                <img src="{{{{ asset('images/logo-dpmptsp.png') }}}}" alt="Logo DPMPTSP Sumatera Utara">
            </div>

            <div id="adminProfile" class="sidebar-profile">
                <button type="button" id="adminProfileToggle" class="profile-toggle">
                    <span class="profile-avatar overflow-hidden p-0 border-0 bg-transparent">
                        <img src="{{{{ Auth::user()?->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()?->name ?? 'Siti') . '&background=FFD54F&color=145239' }}}}" alt="Profile" class="w-full h-full object-cover">
                    </span>

                    <span class="profile-copy">
                        <span class="profile-name">
                            {{{{ Auth::user()?->name ?? 'Siti' }}}}
                        </span>
                        <span class="profile-role">
                            {{{{ auth()->user()->role ?? 'operator' }}}}
                        </span>
                    </span>

                    <i class="fa-solid fa-chevron-down profile-chevron"></i>
                </button>

                <div class="profile-dropdown">
                    <div class="profile-dropdown-inner">
                        <a href="{{{{ route('operator.profile') }}}}" class="profile-dropdown-link {{{{ request()->routeIs('operator.profile') ? 'active' : '' }}}}">
                            <i class="fa-regular fa-user"></i>
                            <span>Profile</span>
                        </a>

                        <a href="{{{{ route('operator.settings') }}}}" class="profile-dropdown-link {{{{ request()->routeIs('operator.settings') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-gear"></i>
                            <span>Settings</span>
                        </a>

                        <div class="profile-dropdown-divider"></div>

                        <button type="button" id="openLogoutModal" class="profile-dropdown-link logout">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </button>
                    </div>
                </div>
            </div>

            <nav class="sidebar-content">
                <div class="sidebar-section-title">
                    Menu Operator
                </div>

                <ul class="sidebar-menu">
                    <li>
                        <a href="{{{{ route('operator.dashboard') }}}}" class="sidebar-link {{{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-table-cells-large"></i>
                            <span>Dashboard Operator</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{{{ route('operator.lq.index') }}}}" class="sidebar-link {{{{ request()->routeIs('operator.lq.index') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-chart-line"></i>
                            <span>Analisis LQ</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{{{ route('operator.ss.index') }}}}" class="sidebar-link {{{{ request()->routeIs('operator.ss.index') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-chart-pie"></i>
                            <span>Analisis SS</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{{{ route('operator.tipologi.index') }}}}" class="sidebar-link {{{{ request()->routeIs('operator.tipologi.index') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-layer-group"></i>
                            <span>Analisis Tipologi Sektor</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{{{ route('operator.klassen.index') }}}}" class="sidebar-link {{{{ request()->routeIs('operator.klassen.index') ? 'active' : '' }}}}">
                            <i class="fa-solid fa-chart-bar"></i>
                            <span>Analisis Klassen</span>
                        </a>
                    </li>
                </ul>

                <div class="sidebar-section-title">
                    Menu Utama
                </div>

                <ul class="sidebar-menu">
                    <li>
                        <a href="{{{{ url('/') }}}}" class="sidebar-link">
                            <i class="fa-solid fa-house"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <main class="admin-main">
            <div class="p-4 md:p-6 lg:p-8 w-full space-y-6 flex-1">
                @yield('content')
            </div>
        </main>
    </div>

    <form id="logoutForm" action="{{{{ route('logout') }}}}" method="POST" hidden>
        @csrf
    </form>

    <div id="logoutModal" class="logout-modal" hidden>
        <div class="logout-modal-backdrop" data-close-logout></div>
        <div class="logout-modal-card">
            <div class="logout-modal-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
            </div>
            <h3>Keluar dari akun?</h3>
            <p>Anda akan keluar dari halaman operator dan perlu login kembali untuk mengakses dashboard.</p>
            <div class="logout-modal-actions">
                <button type="button" class="logout-cancel" data-close-logout>Batal</button>
                <button type="button" id="confirmLogout" class="logout-confirm">Ya, Keluar</button>
            </div>
        </div>
    </div>

    @stack('scripts')

    {admin_js}

    {operator_scripts}
</body>
</html>
'''

with open('resources/views/partials/layouts/operator.blade.php', 'w', encoding='utf-8') as f:
    f.write(new_operator)
