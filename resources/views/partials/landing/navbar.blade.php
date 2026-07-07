<header
    class="main-header"
    id="mainHeader"
>

    <div class="container navbar-container">


        {{-- =====================================================
             BRAND
        ====================================================== --}}

        <a
            href="{{ route('home') }}"
            class="brand"
        >

            <img
                src="{{ asset('images/logo-dpmptsp.png') }}"
                alt="Logo DPMPTSP Sumatera Utara"
                class="brand-logo"
            >


            <div class="brand-text">

                <strong>
                    DPMPTSP
                </strong>

                <span>
                    PROVINSI SUMATERA UTARA
                </span>

            </div>

        </a>



        {{-- =====================================================
             MOBILE BUTTON
        ====================================================== --}}

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

                Beranda

            </a>



            {{-- TENTANG --}}

            <a
                href="{{ route('about') }}"
                class="nav-link
                {{ request()->routeIs('about') ? 'active' : '' }}"
            >

                Tentang

            </a>



            {{-- DASHBOARD --}}

            <a
                href="{{ route('dashboard') }}"
                class="nav-link
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >

                Dashboard

            </a>



            {{-- PETA INVESTASI --}}

            <a
                href="{{ route('investment.map') }}"
                class="nav-link
                {{ request()->routeIs('investment.map') ? 'active' : '' }}"
            >

                Peta Investasi

            </a>



            {{-- KONTAK --}}

            <a
                href="{{ route('contact') }}"
                class="nav-link
                {{ request()->routeIs('contact') ? 'active' : '' }}"
            >

                Kontak

            </a>



            {{-- FAQ --}}

            <a
                href="{{ route('faq') }}"
                class="nav-link
                {{ request()->routeIs('faq') ? 'active' : '' }}"
            >

                FAQ

            </a>


        </nav>



        {{-- =====================================================
             LOGIN / DASHBOARD BUTTON
        ====================================================== --}}

        <div class="navbar-action">


            {{-- BELUM LOGIN --}}

            @guest

                <a
                    href="{{ route('login') }}"
                    class="login-button"
                >

                    <i class="fa-regular fa-user"></i>

                    <span>
                        Login
                    </span>

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