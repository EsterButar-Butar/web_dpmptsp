<header class="main-header" id="mainHeader">

    <div class="container navbar-container">

<<<<<<< HEAD
        {{-- BRAND --}}
        <a href="#hero" class="brand">
=======

        {{-- =====================================================
             BRAND
        ====================================================== --}}

        <a
            href="{{ route('home') }}"
            class="brand"
        >
>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151

            <img
                src="{{ asset('images/logo-dpmptsp.png') }}"
                alt="Logo DPMPTSP Sumatera Utara"
                class="brand-logo"
            >

            <div class="brand-text">
                <strong>DPMPTSP</strong>
                <span>Provinsi Sumatera Utara</span>
            </div>

        </a>

<<<<<<< HEAD
        {{-- MOBILE BUTTON --}}
=======


        {{-- =====================================================
             MOBILE BUTTON
        ====================================================== --}}

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
        <button
            type="button"
            class="mobile-menu-button"
            id="mobileMenuButton"
            aria-label="Buka menu navigasi"
            aria-controls="mainNavigation"
            aria-expanded="false"
        >
            <i class="fa-solid fa-bars"></i>
        </button>

<<<<<<< HEAD
        {{-- NAVIGATION --}}
        <nav class="main-navigation" id="mainNavigation">

            {{-- BERANDA --}}
            <a href="#hero" class="nav-link active">
=======


        {{-- =====================================================
             NAVIGATION
        ====================================================== --}}

        <nav
            class="main-navigation"
            id="mainNavigation"
        >


            {{-- BERANDA --}}

            <a
                href="{{ route('home') }}"
                class="nav-link
                {{ request()->routeIs('home') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                Beranda
            </a>

<<<<<<< HEAD
            {{-- TENTANG --}}
            <a href="#tentang" class="nav-link">
=======


            {{-- TENTANG --}}

            <a
                href="{{ route('about') }}"
                class="nav-link
                {{ request()->routeIs('about') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                Tentang
            </a>

<<<<<<< HEAD
            {{-- DASHBOARD --}}
            <a href="{{ route('dashboard') }}" class="nav-link">
=======


            {{-- DASHBOARD --}}

            <a
                href="{{ route('dashboard') }}"
                class="nav-link
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                Dashboard
            </a>

<<<<<<< HEAD
            {{-- PETA --}}
            <a href="{{ route('investment.map') }}" class="nav-link">
=======


            {{-- PETA INVESTASI --}}

            <a
                href="{{ route('investment.map') }}"
                class="nav-link
                {{ request()->routeIs('investment.map') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                Peta Investasi
            </a>

<<<<<<< HEAD
            {{-- KONTAK --}}
            <a href="#contact" class="nav-link">
=======


            {{-- KONTAK --}}

            <a
                href="{{ route('contact') }}"
                class="nav-link
                {{ request()->routeIs('contact') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                Kontak
            </a>

<<<<<<< HEAD
            {{-- FAQ --}}
            <a href="#faq" class="nav-link">
=======


            {{-- FAQ --}}

            <a
                href="{{ route('faq') }}"
                class="nav-link
                {{ request()->routeIs('faq') ? 'active' : '' }}"
            >

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                FAQ
            </a>


        </nav>

<<<<<<< HEAD
        {{-- LOGIN --}}
=======


        {{-- =====================================================
             LOGIN / DASHBOARD BUTTON
        ====================================================== --}}

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
        <div class="navbar-action">


<<<<<<< HEAD
                <a href="{{ route('dashboard') }}" class="login-button">
                    <i class="fa-solid fa-grip"></i>
                    Dashboard
                </a>

            @else
=======
            {{-- BELUM LOGIN --}}

            @guest
>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151

                <a href="{{ route('login') }}" class="login-button">
                    <i class="fa-regular fa-user"></i>
<<<<<<< HEAD
                    Login
=======

                    <span>
                        Login
                    </span>

>>>>>>> 5e82d98ad9d6396fc843531b4d3d1dfe2ad1d151
                </a>


            {{-- SUDAH LOGIN --}}

            @else

                <a
                    href="{{ route('dashboard') }}"
                    class="login-button"
                >

                    <i class="fa-solid fa-gauge-high"></i>

                    <span>
                        Dashboard
                    </span>

                </a>

            @endguest


        </div>

    </div>

</header>