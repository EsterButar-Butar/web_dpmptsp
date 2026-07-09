<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <title>@yield('title', 'Admin') | DPMPTSP Sumatera Utara</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    {{-- Font Awesome --}}
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    {{-- Vite --}}
    @vite([
        'resources/css/app.css',
        'resources/js/app.js'
    ])

    <style>
        :root {
            --green-dark: #255d3e;
            --green-main: #2f6b48;
            --green-soft: #eaf7ef;
            --green-pale: #f6fbf7;
            --yellow-main: #f4cf63;
            --yellow-soft: #fff8e6;
            --navy: #14213d;
            --text-dark: #243042;
            --text-soft: #667085;
            --border-soft: #e5e7eb;
            --bg-main: #f8faf8;
            --white: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: var(--bg-main);
            color: var(--text-dark);
        }

        body {
            min-height: 100vh;
        }

        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .admin-sidebar {
            width: 290px;
            background: var(--white);
            border-right: 1px solid var(--border-soft);
            padding: 24px 22px;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            overflow-y: auto;
            z-index: 20;
        }

        .admin-logo {
            display: flex;
            align-items: center;
            margin-bottom: 26px;
        }

        .admin-logo img {
            width: 160px;
            height: auto;
            object-fit: contain;
            display: block;
        }

        .admin-profile {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            padding: 12px 0 6px;
        }

        .admin-avatar {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--green-dark);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .admin-name {
            font-size: 15px;
            font-weight: 700;
            color: #101828;
            line-height: 1.3;
            margin-bottom: 2px;
        }

        .admin-role {
            font-size: 14px;
            font-weight: 400;
            color: var(--text-soft);
        }

        .menu-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--navy);
            margin: 22px 0 12px;
        }

        .sidebar-menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sidebar-menu li {
            margin-bottom: 6px;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--navy);
            padding: 14px 14px;
            border-radius: 16px;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .sidebar-link i {
            width: 22px;
            text-align: center;
            font-size: 18px;
        }

        .sidebar-link:hover {
            background: var(--green-pale);
            color: var(--green-dark);
        }

        .sidebar-link.active {
            background: #e5f1e8;
            color: var(--green-dark);
        }

        .admin-main {
            margin-left: 290px;
            width: calc(100% - 290px);
            min-height: 100vh;
            background: var(--bg-main);
        }

        @media (max-width: 992px) {
            .admin-wrapper {
                flex-direction: column;
            }

            .admin-sidebar {
                position: relative;
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border-soft);
            }

            .admin-main {
                margin-left: 0;
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="admin-wrapper">
        <aside class="admin-sidebar">
            <div class="admin-logo">
                <img
                    src="{{ asset('images/logo-dpmptsp.png') }}"
                    alt="Logo DPMPTSP"
                >
            </div>

            <div class="admin-profile">
                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>

                <div>
                    <div class="admin-name">
                        {{ auth()->user()->name ?? 'Admin' }}
                    </div>
                    <div class="admin-role">
                        {{ ucfirst(auth()->user()->role ?? 'admin') }}
                    </div>
                </div>
            </div>

            <div class="menu-title">Menu Utama</div>
            <ul class="sidebar-menu">
                <li>
                    <a
                        href="{{ route('home') }}"
                        class="sidebar-link"
                    >
                        <i class="fa-solid fa-house"></i>
                        <span>Beranda</span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('about') }}"
                        class="sidebar-link"
                    >
                        <i class="fa-solid fa-circle-info"></i>
                        <span>Tentang</span>
                    </a>
                </li>
            </ul>

            <div class="menu-title">Menu Admin</div>
            <ul class="sidebar-menu">
                <li>
                    <a
                        href="{{ route('admin.dashboard') }}"
                        class="sidebar-link {{ request()->is('admin/dashboard*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li>
                    <a
                        href="{{ route('admin.pengguna.index') }}"
                        class="sidebar-link {{ request()->is('admin/pengguna*') ? 'active' : '' }}"
                    >
                        <i class="fa-solid fa-users"></i>
                        <span>Pengguna</span>
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="sidebar-link"
                    >
                        <i class="fa-solid fa-map"></i>
                        <span>Data Wilayah</span>
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="sidebar-link"
                    >
                        <i class="fa-solid fa-table-cells"></i>
                        <span>Kode KBLI</span>
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="sidebar-link"
                    >
                        <i class="fa-solid fa-qrcode"></i>
                        <span>Kode HS</span>
                    </a>
                </li>
            </ul>
        </aside>

        <main class="admin-main">
            @yield('content')
        </main>
    </div>
</body>
</html>