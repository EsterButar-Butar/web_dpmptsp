<header
    class="main-header"
    id="mainHeader"
>

    <div class="container navbar-container">


        {{-- BRAND --}}

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



        {{-- MOBILE BUTTON --}}

        <button
            type="button"
            class="mobile-menu-button"
            id="mobileMenuButton"
            aria-label="Buka menu navigasi"
        >

            <i class="fa-solid fa-bars"></i>

        </button>



        {{-- NAVIGATION --}}

        <nav
            class="main-navigation"
            id="mainNavigation"
        >


            <a
                href="{{ route('home') }}"
                class="nav-link
                {{ request()->routeIs('home') ? 'active' : '' }}"
            >

                Beranda

            </a>


            <a
                href="{{ route('about') }}"
                class="nav-link
                {{ request()->routeIs('about') ? 'active' : '' }}"
            >

                Tentang

            </a>


            <a
                href="{{ route('dashboard') }}"
                class="nav-link
                {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            >

                Dashboard

            </a>


            <a
                href="{{ route('investment.map') }}"
                class="nav-link
                {{ request()->routeIs('investment.map') ? 'active' : '' }}"
            >

                Peta Investasi

            </a>


            <a
                href="{{ route('contact') }}"
                class="nav-link
                {{ request()->routeIs('contact') ? 'active' : '' }}"
            >

                Kontak

            </a>


            <a
                href="{{ route('faq') }}"
                class="nav-link
                {{ request()->routeIs('faq') ? 'active' : '' }}"
            >

                FAQ

            </a>

        </nav>



        {{-- LOGIN --}}

        <div class="navbar-action">

            @auth

                <a
                    href="{{ route('dashboard') }}"
                    class="login-button"
                >

                    <i class="fa-solid fa-grip"></i>

                    Dashboard

                </a>

            @else

                <a
                    href="{{ route('login') }}"
                    class="login-button"
                >

                    <i class="fa-regular fa-user"></i>

                    Login

                </a>

            @endauth

        </div>


    </div>

</header>