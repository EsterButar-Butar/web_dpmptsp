<aside class="user-sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">

        <a href="{{ route('home') }}">

            <img
                src="{{ asset('images/logo-dpmptsp.png') }}"
                alt="Logo DPMPTSP Sumatera Utara"
            >

        </a>

    </div>


    {{-- USER INFORMATION --}}
    <div class="sidebar-profile">

        <div class="sidebar-avatar">

            @if (!empty(auth()->user()->avatar))

                <img
                    src="{{ auth()->user()->avatar }}"
                    alt="Foto profil"
                >

            @else

                <div class="sidebar-avatar-default">

                    {{ strtoupper(
                        substr(auth()->user()->name ?? 'U', 0, 1)
                    ) }}

                </div>

            @endif

        </div>


        <h3>
            {{ \Illuminate\Support\Str::before(
                auth()->user()->name ?? 'Pengguna',
                ' '
            ) }}
        </h3>

        <span>
            {{ ucfirst(auth()->user()->role ?? 'User') }}
        </span>

    </div>


    {{-- NAVIGATION --}}
    <nav class="sidebar-navigation">

        <p class="sidebar-section-title">
            Menu Utama
        </p>


        {{-- HOME --}}
        <a
            href="{{ route('home') }}"
            class="sidebar-link"
        >
            <i class="fa-solid fa-house"></i>

            <span>
                Beranda
            </span>
        </a>


        {{-- ABOUT --}}
        <a
            href="{{ route('about') }}"
            class="sidebar-link"
        >
            <i class="fa-solid fa-circle-info"></i>

            <span>
                Tentang
            </span>
        </a>


        {{-- PROFILE --}}
        <a
            href="{{ route('profile.edit') }}"
            class="sidebar-link active"
        >
            <i class="fa-regular fa-user"></i>

            <span>
                Profil
            </span>
        </a>


        {{-- SETTINGS --}}
        <p class="sidebar-section-title settings-title">
            Pengaturan
        </p>


        {{-- PASSWORD --}}
        <a
            href="{{ route('password.request') }}"
            class="sidebar-link"
        >
            <i class="fa-solid fa-key"></i>

            <span>
                Ganti Kata Sandi
            </span>
        </a>


        {{-- LOGOUT --}}
        <form
            method="POST"
            action="{{ route('logout') }}"
        >

            @csrf

            <button
                type="submit"
                class="sidebar-link sidebar-logout"
            >

                <i class="fa-solid fa-arrow-right-from-bracket"></i>

                <span>
                    Keluar
                </span>

            </button>

        </form>

    </nav>

</aside>