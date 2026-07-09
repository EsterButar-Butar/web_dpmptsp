@extends('layouts.profile')

@section('title', 'Profil Saya | DPMPTSP Provinsi Sumatera Utara')

@section('content')

<section class="profile-page">

    {{-- =====================================================
         HEADER PROFIL
    ====================================================== --}}
    <header class="profile-header">

        <div class="profile-header-container">

            {{-- KEMBALI KE BERANDA --}}
            <a
                href="{{ route('home') }}"
                class="profile-back-button"
            >
                <i class="fa-solid fa-arrow-left"></i>

                <span>
                    Kembali ke beranda
                </span>
            </a>


            {{-- IDENTITAS USER --}}
            <div class="profile-user">

                <div class="profile-user-text">

                    <h1>
                        {{ ucwords($user->name ?? 'Pengguna') }}
                    </h1>

                    <span>
                        As {{ ucfirst($user->role ?? 'User') }}
                    </span>

                </div>


                {{-- AVATAR --}}
                <div class="profile-avatar">

                    @if(!empty($user->avatar))

                        <img
                            src="{{ $user->avatar }}"
                            alt="Foto Profil"
                        >

                    @else

                        <div class="profile-avatar-default">

                            {{ strtoupper(
                                substr($user->name ?? 'U', 0, 1)
                            ) }}

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </header>


    {{-- =====================================================
         KONTEN PROFIL
    ====================================================== --}}
    <main class="profile-content">


        {{-- =================================================
             INFORMASI
        ================================================== --}}
        <section class="profile-section">

            <h2 class="profile-section-title">
                Informasi
            </h2>


            <div class="profile-information-grid">


                {{-- NAMA --}}
                <div class="profile-info-item">

                    <span class="profile-info-label">
                        Nama Lengkap
                    </span>

                    <strong>
                        {{ $user->name ?? '-' }}
                    </strong>

                </div>


                {{-- EMAIL --}}
                <div class="profile-info-item">

                    <span class="profile-info-label">
                        Alamat Email
                    </span>

                    <strong>
                        {{ $user->email ?? '-' }}
                    </strong>

                </div>


                {{-- ROLE --}}
                <div class="profile-info-item">

                    <span class="profile-info-label">
                        Role
                    </span>

                    <strong>
                        {{ ucfirst($user->role ?? 'User') }}
                    </strong>

                </div>


                {{-- STATUS EMAIL --}}
                <div class="profile-info-item">

                    <span class="profile-info-label">
                        Status Email
                    </span>


                    @if(!empty($user->email_verified_at))

                        <strong class="status-verified">

                            <i class="fa-solid fa-circle-check"></i>

                            Terverifikasi

                        </strong>

                    @else

                        <strong class="status-unverified">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            Belum Terverifikasi

                        </strong>

                    @endif

                </div>


                {{-- BERGABUNG SEJAK --}}
                <div class="profile-info-item">

                    <span class="profile-info-label">
                        Bergabung Sejak
                    </span>

                    <strong>

                        {{ $user->created_at
                            ? $user->created_at->format('d F Y')
                            : '-'
                        }}

                    </strong>

                </div>

            </div>

        </section>


        {{-- =================================================
             PENGATURAN
        ================================================== --}}
        <section class="profile-settings">

            <h2 class="profile-settings-title">
                Pengaturan
            </h2>


            {{-- GANTI KATA SANDI --}}
            <a
                href="{{ route('password.request') }}"
                class="profile-setting-link"
            >

                <span>
                    Ganti kata sandi
                </span>

                <i class="fa-solid fa-chevron-right"></i>

            </a>


            {{-- LOGOUT --}}
            <form
                method="POST"
                action="{{ route('logout') }}"
                class="profile-logout-form"
            >

                @csrf

                <button
                    type="submit"
                    class="profile-logout-button"
                >
                    Keluar
                </button>

            </form>

        </section>

    </main>

</section>

@endsection